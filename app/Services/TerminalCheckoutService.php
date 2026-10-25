<?php

namespace App\Services;

use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class TerminalCheckoutService
{
    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['terminal_shipping' => $message]);
    }

    public function preview(): bool
    {
        return (int) session('terminal_checkout_preview_until', 0) > time();
    }

    public function available(WebsiteSetting $setting): bool
    {
        return $this->preview() || ($setting->delivery_provider === 'terminal_africa'
            && (bool) $setting->terminal_checkout_enabled
            && $setting->terminal_environment === 'live'
            && (bool) $setting->terminal_checkout_confirmed
            && count($this->allowed($setting)) > 0);
    }

    public function allowed(WebsiteSetting $setting): array
    {
        $value = json_decode((string) $setting->terminal_checkout_services, true);
        return is_array($value) ? array_values(array_filter($value, 'is_string')) : [];
    }

    public function client(WebsiteSetting $setting)
    {
        $environment = $this->preview() ? 'test' : 'live';
        try {
            $secret = ($setting->terminal_credentials ?? [])[$environment] ?? '';
        } catch (\Throwable $e) {
            $this->fail('Re-save the Terminal Africa secret key in Delivery Settings.');
        }
        if (!is_string($secret) || !str_starts_with($secret, 'sk_'.$environment.'_')) {
            $this->fail('Save the correct Terminal Africa '.$environment.' secret key first.');
        }
        return Http::baseUrl($environment === 'test'
            ? 'https://sandbox.terminal.africa/v1' : 'https://api.terminal.africa/v1')
            ->withToken($secret)->acceptJson()->asJson()->connectTimeout(10)->timeout(30)
            ->withOptions(['allow_redirects' => false]);
    }

    private function data($response, string $stage): array
    {
        if (!$response->successful() || $response->json('status') !== true
            || !is_array($response->json('data'))) {
            // Provider bodies and HTTP exceptions can contain secrets or personal data.
            $this->fail('Terminal Africa could not complete '.$stage.' (HTTP '.$response->status().'). Check your location and saved settings.');
        }
        return $response->json('data');
    }

    private function id(array $data, string $field): string
    {
        $id = $data[$field] ?? $data['id'] ?? null;
        if (!is_string($id) || $id === '') {
            $this->fail('Terminal Africa returned an incomplete response. Please request rates again.');
        }
        return $id;
    }

    public function cities(WebsiteSetting $setting, string $state): array
    {
        $environment = $this->preview() ? 'test' : 'live';
        return Cache::remember('terminal-cities:'.$environment.':'.hash('sha256', $state), 21600, function () use ($setting, $state) {
            $client = $this->client($setting);
            $states = $this->data($client->get('/states', ['country_code' => 'NG']), 'state lookup');
            $normalize = fn ($name) => mb_strtolower(trim(preg_replace('/\s+state$/i', '', $name)));
            $match = collect($states)->first(fn ($row) => is_array($row)
                && $normalize((string) ($row['name'] ?? '')) === $normalize($state));
            if (!$match || empty($match['isoCode'])) {
                $this->fail('Terminal Africa could not match this state. Please choose another state or contact us.');
            }
            $cities = $this->data($client->get('/cities', [
                'country_code' => 'NG', 'state_code' => $match['isoCode'],
            ]), 'city lookup');
            return collect($cities)->pluck('name')->filter(fn ($name) => is_string($name) && $name !== '')
                ->unique()->sort()->values()->all();
        });
    }

    public function context(WebsiteSetting $setting, $cart, array $destination): array
    {
        if (!$this->available($setting)) {
            $this->fail('Terminal Africa checkout is currently unavailable.');
        }
        $packageId = $this->preview()
            ? session('terminal_checkout_preview_package') : $setting->terminal_checkout_package_id;
        $package = ShippingPackage::where('is_active', true)->find($packageId);
        if (!$package) {
            $this->fail('Select an active checkout packaging preset in Terminal Checkout Settings.');
        }
        $limit = $this->preview() ? 1 : (int) $setting->terminal_checkout_max_units;
        if ($cart->isEmpty() || $cart->sum('quantity') > $limit || $limit < 1) {
            $this->fail($this->preview()
                ? 'Start this sandbox checkout test with one item, quantity 1.'
                : 'This cart needs a custom packing quote. Please contact us or use store pickup.');
        }
        $items = [];
        foreach ($cart as $item) {
            $weight = (float) $item->attributes->get('shipping_weight_kg');
            if (!is_finite($weight) || $weight <= 0 || $weight > 1000) {
                $this->fail('An item is missing its shipping weight. Please contact us before placing this order.');
            }
            // One entry per physical unit avoids ambiguity over total versus unit value/weight.
            for ($unit = 0; $unit < $item->quantity; $unit++) {
                $items[] = [
                    'name' => mb_substr($item->name, 0, 150),
                    'description' => mb_substr($item->name, 0, 255),
                    'type' => 'parcel', 'currency' => 'NGN',
                    'value' => round((float) $item->price, 2),
                    'quantity' => 1, 'weight' => round($weight, 3),
                    'local_product' => $item->attributes->get('product_id'),
                    'local_variant' => $item->attributes->get('variant_id'),
                ];
            }
        }
        $percentage = (float) $setting->shipping_markup_percentage;
        $fee = $this->preview() ? 0 : (float) $setting->terminal_checkout_extra_fee;
        if (!is_finite($percentage) || $percentage < 0 || $percentage > 100 || !is_finite($fee) || $fee < 0) {
            $this->fail('The saved shipping markup or additional fee is invalid.');
        }
        $pickup = [
            'city' => (string) $setting->dispatch_city,
            'state' => (string) $setting->dispatch_state,
            'country' => (string) $setting->dispatch_country,
        ];
        if (!$pickup['city'] || !$pickup['state'] || $pickup['country'] !== 'NG') {
            $this->fail('Complete the dispatch city, state and country in Delivery Settings.');
        }
        $context = [
            'environment' => $this->preview() ? 'test' : 'live', 'items' => $items,
            'destination' => $destination, 'pickup' => $pickup,
            'package' => [
                'id' => $package->id, 'name' => $package->name,
                'length' => (float) $package->length_cm, 'width' => (float) $package->width_cm,
                'height' => (float) $package->height_cm, 'weight' => (float) $package->empty_weight_kg,
            ],
            'markup' => $percentage, 'extra_fee' => round($fee, 2),
            'allowed' => $this->allowed($setting),
            // Hash so a key rotation invalidates a saved quote; never retain the key itself.
            'key_fingerprint' => hash('sha256', (string) (($setting->terminal_credentials ?? [])[$this->preview() ? 'test' : 'live'] ?? '')),
        ];
        return $context;
    }

    public function fingerprint(array $context): string
    {
        return hash('sha256', json_encode($context, JSON_THROW_ON_ERROR));
    }

    public function quote(WebsiteSetting $setting, array $context): array
    {
        $client = $this->client($setting);
        $package = $context['package'];
        unset($package['id']);
        $package += ['type' => 'box', 'size_unit' => 'cm', 'weight_unit' => 'kg'];
        $packagingId = $this->id($this->data($client->post('/packaging', $package), 'packaging'), 'packaging_id');
        $pickupId = $this->id($this->data($client->post('/addresses', $context['pickup']), 'pickup address'), 'address_id');
        $destination = array_intersect_key($context['destination'], array_flip(['city', 'state', 'country', 'zip']));
        $destinationId = $this->id($this->data($client->post('/addresses', $destination), 'delivery address'), 'address_id');
        $items = array_map(function ($item) {
            unset($item['local_product'], $item['local_variant']);
            return $item;
        }, $context['items']);
        $parcelId = $this->id($this->data($client->post('/parcels', [
            'description' => 'De Lamoure checkout parcel', 'weight_unit' => 'kg',
            'packaging' => $packagingId, 'items' => $items,
        ]), 'parcel'), 'parcel_id');
        $raw = $this->data($client->timeout(90)->get('/rates/shipment', [
            'pickup_address' => $pickupId, 'delivery_address' => $destinationId,
            'parcel_id' => $parcelId, 'currency' => 'NGN',
        ]), 'rates');
        $rates = [];
        foreach ($raw as $rate) {
            if (!is_array($rate) || ($rate['currency'] ?? '') !== 'NGN'
                || !is_numeric($rate['amount'] ?? null)) continue;
            $amount = (float) $rate['amount'];
            $rateId = $rate['rate_id'] ?? $rate['id'] ?? '';
            if (!is_string($rateId) || !$rateId || !is_finite($amount) || $amount <= 0 || $amount > 100000000) continue;
            // Exact service identity, never a carrier name alone. Empty references cannot go live.
            $reference = (string) ($rate['carrier_reference'] ?? '');
            $description = (string) ($rate['carrier_rate_description'] ?? '');
            $serviceKey = $reference !== '' && $description !== ''
                ? hash('sha256', $reference.'|'.$description) : '';
            if ($context['environment'] === 'live' && (! $serviceKey || !in_array($serviceKey, $context['allowed'], true))) continue;
            $baseKobo = (int) round($amount * 100);
            $feeKobo = (int) round($context['extra_fee'] * 100);
            $markupKobo = (int) round(($baseKobo + $feeKobo) * $context['markup'] / 100);
            $rates[] = [
                'id' => $rateId, 'service_key' => $serviceKey,
                'carrier' => (string) ($rate['carrier_name'] ?? 'Carrier'),
                'service' => $description, 'estimate' => (string) ($rate['delivery_time'] ?? ''),
                'base_cost' => ($baseKobo + $feeKobo) / 100,
                'api_amount' => $baseKobo / 100, 'extra_fee' => $feeKobo / 100,
                'markup_percentage' => $context['markup'], 'markup_amount' => $markupKobo / 100,
                'total' => ($baseKobo + $feeKobo + $markupKobo) / 100,
            ];
        }
        return ['rates' => $rates, 'parcel_id' => $parcelId,
            'pickup_address_id' => $pickupId, 'delivery_address_id' => $destinationId];
    }
}
