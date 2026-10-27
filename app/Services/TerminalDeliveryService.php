<?php
namespace App\Services;

use App\Models\Order;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class TerminalDeliveryService
{
    private function client()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $secret = trim((string) (($setting->terminal_credentials ?? [])['live'] ?? ''));
        if ($secret === '') throw new RuntimeException('The live secret is missing.');
        return Http::baseUrl('https://api.terminal.africa/v1')->withToken($secret)
            ->acceptJson()->asJson()->connectTimeout(10)->timeout(90)
            ->withOptions(['allow_redirects' => false]);
    }

    private function data($response, string $stage): array
    {
        if (!$response->successful() || $response->json('status') !== true || !is_array($response->json('data'))) {
            throw new RuntimeException($stage.' failed (HTTP '.$response->status().'). Review in Terminal before retrying.');
        }
        return $response->json('data');
    }

    public function register(Order $order): void
    {
        if ($order->payment_status !== 'paid' || $order->status !== 'processing'
            || $order->delivery_method !== 'shipping') return;
        $raw = $order->getRawOriginal('terminal_shipping_snapshot');
        $snapshot = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($snapshot) || ($snapshot['environment'] ?? '') !== 'live') return;
        DB::table('terminal_deliveries')->insertOrIgnore([
            'order_id' => $order->id, 'state' => 'ready', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function book(int $orderId, bool $manual = false): void
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        if (!$manual && !$setting->terminal_auto_book) return;
        // Atomic claim: duplicate callbacks/workers cannot book the same order twice.
        $claim = DB::transaction(function () use ($orderId, $setting) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            if ($order->payment_status !== 'paid' || $order->status !== 'processing' || $order->delivery_method !== 'shipping') return null;
            $row = DB::table('terminal_deliveries')->where('order_id', $orderId)->lockForUpdate()->first();
            if (!$row || !in_array($row->state, ['ready', 'failed'], true)) return null;
            $raw = $order->getRawOriginal('terminal_shipping_snapshot');
            $snapshot = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($snapshot) || ($snapshot['environment'] ?? '') !== 'live') return null;
            if ($setting->terminal_environment !== 'live') {
                $this->update($orderId, ['state' => 'failed', 'last_error' => 'Save your live delivery settings before booking.']);
                return null;
            }
            $this->update($orderId, ['state' => 'booking', 'attempted_at' => now(), 'last_error' => null]);
            return [$order, $snapshot, $row->shipment_id];
        });
        if (!$claim) return;
        [$order, $snapshot, $shipmentId] = $claim;
        $remoteMutationStarted = false;
        try {
            foreach (['id', 'parcel_id', 'pickup_address_id', 'delivery_address_id', 'pickup_snapshot', 'destination_snapshot'] as $key) {
                if (empty($snapshot[$key])) throw new RuntimeException('This order lacks a complete live shipping snapshot. Arrange it manually.');
            }
            $pickup = $snapshot['pickup_snapshot'];
            $destination = $snapshot['destination_snapshot'];
            $parts = preg_split('/\s+/', trim((string) ($pickup['name'] ?? '')), 2);
            $pickup['first_name'] = $parts[0] ?? '';
            $pickup['last_name'] = $parts[1] ?? '';
            $destination = array_intersect_key($destination, array_flip(['country', 'state', 'city', 'zip', 'line1']));
            $destination += ['first_name' => $order->first_name, 'last_name' => $order->last_name,
                'email' => $order->email, 'phone' => $order->phone];
            foreach ([$pickup, $destination] as $address) {
                foreach (['first_name', 'last_name', 'phone', 'line1', 'city', 'state', 'country', 'zip'] as $field) {
                    if (trim((string) ($address[$field] ?? '')) === '') throw new RuntimeException('Complete sender and recipient names, phones, addresses and postal codes before booking.');
                }
            }
            $client = $this->client();
            $this->data($client->put('/addresses/'.rawurlencode($snapshot['pickup_address_id']), $pickup), 'Pickup address update');
            $this->data($client->put('/addresses/'.rawurlencode($snapshot['delivery_address_id']), $destination), 'Delivery address update');
            if (!$shipmentId) {
                // A lost response may mean the draft exists. Never blindly recreate it.
                $remoteMutationStarted = true;
                $draft = $this->data($client->post('/shipments', [
                    'address_from' => $snapshot['pickup_address_id'], 'address_to' => $snapshot['delivery_address_id'],
                    'parcel' => $snapshot['parcel_id'], 'shipment_purpose' => 'commercial',
                    'metadata' => ['order_id' => $order->id, 'order_no' => $order->order_no],
                ]), 'Shipment creation');
                $shipmentId = $draft['shipment_id'] ?? null;
                if (!is_string($shipmentId) || $shipmentId === '') throw new RuntimeException('The draft response had no shipment reference.');
                $this->update($orderId, ['shipment_id' => $shipmentId, 'provider_status' => 'draft']);
            }
            $remoteMutationStarted = true;
            $booked = $this->data($client->post('/shipments/pickup', [
                'shipment_id' => $shipmentId, 'rate_id' => $snapshot['id'], 'purchase_insurance' => false,
            ]), 'Pickup booking');
            $this->saveRemote($orderId, $booked, $shipmentId);
        } catch (Throwable $e) {
            // Only our own bounded diagnostics are retained; provider exception bodies can contain PII/secrets.
            $message = $e instanceof RuntimeException && !$e instanceof \Illuminate\Http\Client\RequestException
                && !$e instanceof \Illuminate\Http\Client\ConnectionException
                ? $e->getMessage() : 'Provider request could not be completed. Check Terminal before retrying.';
            $this->update($orderId, ['state' => $remoteMutationStarted ? 'review' : 'failed',
                'last_error' => mb_substr($message, 0, 800)]);
        }
    }

    public function sync(int $orderId): void
    {
        $row = DB::table('terminal_deliveries')->where('order_id', $orderId)->first();
        if (!$row || !$row->shipment_id) return;
        $data = $this->data($this->client()->get('/shipments/'.rawurlencode($row->shipment_id)), 'Tracking refresh');
        if (($data['shipment_id'] ?? '') !== $row->shipment_id) throw new RuntimeException('Shipment identity mismatch.');
        $this->saveRemote($orderId, $data, $row->shipment_id);
    }

    private function saveRemote(int $orderId, array $data, string $shipmentId): void
    {
        $status = (string) ($data['status'] ?? 'pending');
        $state = match($status) {
            'delivered' => 'delivered', 'cancelled', 'canceled' => 'cancelled',
            'confirmed', 'pending', 'in-transit' => 'booked', default => 'review',
        };
        $extras = is_array($data['extras'] ?? null) ? $data['extras'] : [];
        $values = ['shipment_id' => $shipmentId, 'provider_status' => $status,
            'state' => $state, 'synced_at' => now(), 'last_error' => $state === 'review' ? 'Review the draft in Terminal. No automatic rebooking will occur.' : null];
        foreach (['tracking_number', 'tracking_url', 'shipping_label'] as $key) {
            $value = $extras[$key] ?? null;
            if (is_string($value) && $value !== '') $values[$key] = mb_substr($value, 0, 2000);
        }
        // Tracking syncs are serialized; a delayed refresh cannot reverse a recorded delivery/cancellation.
        DB::transaction(function () use ($orderId, $values) {
            $row = DB::table('terminal_deliveries')->where('order_id', $orderId)->lockForUpdate()->first();
            if (!$row || in_array($row->state, ['delivered', 'cancelled'], true)) return;
            $this->update($orderId, $values);
        });
    }

    private function update(int $orderId, array $values): void
    {
        DB::table('terminal_deliveries')->where('order_id', $orderId)->update($values + ['updated_at' => now()]);
    }
}
