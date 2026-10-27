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
        throw ValidationException::withMessages([
            'terminal_shipping' => $message,
        ]);
    }

    // Compatibility with existing checkout templates; live operation only.
    public function preview(): bool
    {
        return false;
    }

    public function available(WebsiteSetting $setting): bool
    {
        return $setting->delivery_provider === 'terminal_africa';
    }

    public function client(WebsiteSetting $setting)
    {
        try {
            $secret = trim(
                (string) (($setting->terminal_credentials ?? [])['live'] ?? '')
            );
        } catch (\Throwable $e) {
            $this->fail('Re-save your live secret key in Delivery Settings.');
        }

        if ($secret === '' || preg_match('/[\r\n]/', $secret)) {
            $this->fail('Save your Terminal Africa live secret key.');
        }

        return Http::baseUrl('https://api.terminal.africa/v1')
            ->withToken($secret)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout(30)
            ->withOptions([
                'allow_redirects' => false,
            ]);
    }

    public function carriers(WebsiteSetting $setting): array
    {
        $result = [];
        $page = 1;

        do {
            $data = $this->data(
                $this->client($setting)->get('/users/carriers', [
                    'active' => 'true',
                    'perPage' => 100,
                    'page' => $page,
                ]),
                'carrier lookup'
            );

            foreach (($data['carriers'] ?? []) as $carrier) {
                if (is_array($carrier) && ($carrier['active'] ?? false)) {
                    $result[] = $carrier;
                }
            }

            $pagination = $data['pagination'] ?? [];
            $next = $pagination['nextPage'] ?? null;

            if (! $next && ! empty($pagination['hasNextPage'])) {
                $next = $page + 1;
            }

            if (! $next && $page < (int) ($pagination['pageCount'] ?? 1)) {
                $next = $page + 1;
            }

            if (! is_numeric($next) || (int) $next <= $page) {
                break;
            }

            $page = (int) $next;
        } while ($page <= 100);

        return $result;
    }

    private function data($response, string $stage): array
    {
        if (
            ! $response->successful()
            || $response->json('status') !== true
            || ! is_array($response->json('data'))
        ) {
            // Do not expose provider bodies, credentials or personal data.
            $this->fail(
                'Terminal Africa could not complete '.$stage
                .' (HTTP '.$response->status().'). '
                .'Check your location and saved settings.'
            );
        }

        return $response->json('data');
    }

    private function id(array $data, string $field): string
    {
        $id = $data[$field] ?? $data['id'] ?? null;

        if (! is_string($id) || $id === '') {
            $this->fail(
                'Terminal Africa returned an incomplete response. '
                .'Please request rates again.'
            );
        }

        return $id;
    }

    public function cities(
        WebsiteSetting $setting,
        string $state
    ): array {
        $cacheKey = 'terminal-cities:live:'.hash('sha256', $state);

        return Cache::remember(
            $cacheKey,
            21600,
            function () use ($setting, $state) {
                $client = $this->client($setting);

                $states = $this->data(
                    $client->get('/states', [
                        'country_code' => 'NG',
                    ]),
                    'state lookup'
                );

                $normalize = fn ($name) => mb_strtolower(
                    trim(preg_replace('/\s+state$/i', '', $name))
                );

                $match = collect($states)->first(
                    fn ($row) => is_array($row)
                        && $normalize((string) ($row['name'] ?? ''))
                            === $normalize($state)
                );

                if (! $match || empty($match['isoCode'])) {
                    $this->fail(
                        'Terminal Africa could not match this state. '
                        .'Please choose another state or contact us.'
                    );
                }

                $cities = $this->data(
                    $client->get('/cities', [
                        'country_code' => 'NG',
                        'state_code' => $match['isoCode'],
                    ]),
                    'city lookup'
                );

                return collect($cities)
                    ->pluck('name')
                    ->filter(
                        fn ($name) => is_string($name) && $name !== ''
                    )
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            }
        );
    }

    public function context(
        WebsiteSetting $setting,
        $cart,
        array $destination
    ): array {
        if (! $this->available($setting)) {
            $this->fail('Terminal Africa checkout is currently unavailable.');
        }

        $package = ShippingPackage::where('is_active', true)
            ->find($setting->terminal_checkout_package_id);

        if (! $package) {
            $package = ShippingPackage::where('is_active', true)
                ->orderBy('id')
                ->first();
        }

        if (! $package) {
            $this->fail('Add an active packaging preset for delivery rates.');
        }

        if ($cart->isEmpty()) {
            $this->fail('Your cart is empty.');
        }

        $items = [];

        foreach ($cart as $item) {
            $weight = (float) $item->attributes->get('shipping_weight_kg');

            if (! is_finite($weight) || $weight <= 0 || $weight > 1000) {
                $this->fail(
                    'An item is missing its shipping weight. '
                    .'Please contact us before placing this order.'
                );
            }

            // One entry per physical unit.
            for ($unit = 0; $unit < $item->quantity; $unit++) {
                $items[] = [
                    'name' => mb_substr($item->name, 0, 150),
                    'description' => mb_substr($item->name, 0, 255),
                    'type' => 'parcel',
                    'currency' => 'NGN',
                    'value' => round((float) $item->price, 2),
                    'quantity' => 1,
                    'weight' => round($weight, 3),
                    'local_product' => $item->attributes->get('product_id'),
                    'local_variant' => $item->attributes->get('variant_id'),
                ];
            }
        }

        $percentage = (float) $setting->shipping_markup_percentage;
        $fee = (float) $setting->terminal_checkout_extra_fee;

        if (
            ! is_finite($percentage)
            || $percentage < 0
            || $percentage > 100
            || ! is_finite($fee)
            || $fee < 0
        ) {
            $this->fail(
                'The saved shipping markup or additional fee is invalid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pickup Address
        |--------------------------------------------------------------------------
        */

        $senderName = trim((string) $setting->dispatch_name);

        $nameParts = preg_split('/\s+/', $senderName, 2);

        $phone = preg_replace(
            '/[\s()-]+/',
            '',
            trim((string) $setting->dispatch_phone)
        );

        if (str_starts_with($phone, '234')) {
            $phone = '+'.$phone;
        } elseif (str_starts_with($phone, '0')) {
            $phone = '+234'.substr($phone, 1);
        }

        $pickup = [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'name' => $senderName,
            'email' => trim((string) $setting->dispatch_email),
            'phone' => $phone,
            'line1' => trim((string) $setting->dispatch_line1),
            'zip' => trim((string) $setting->dispatch_postal_code),
            'city' => trim((string) $setting->dispatch_city),
            'state' => trim((string) $setting->dispatch_state),
            'country' => 'NG',
        ];

        if (! $pickup['city'] || ! $pickup['state']) {
            $this->fail(
                'Complete the dispatch city and state in Delivery Settings.'
            );
        }

        return [
            'environment' => 'live',
            'items' => $items,
            'destination' => $destination,
            'pickup' => $pickup,
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'length' => (float) $package->length_cm,
                'width' => (float) $package->width_cm,
                'height' => (float) $package->height_cm,
                'weight' => (float) $package->empty_weight_kg,
            ],
            'markup' => $percentage,
            'extra_fee' => round($fee, 2),
            'provider' => $setting->delivery_provider,

            // Key rotation invalidates saved quotes without retaining the key.
            'key_fingerprint' => hash(
                'sha256',
                (string) (($setting->terminal_credentials ?? [])['live'] ?? '')
            ),
        ];
    }

    public function fingerprint(array $context): string
    {
        return hash(
            'sha256',
            json_encode($context, JSON_THROW_ON_ERROR)
        );
    }

    public function quote(
        WebsiteSetting $setting,
        array $context
    ): array {
        $client = $this->client($setting);

        $package = $context['package'];

        unset($package['id']);

        $package += [
            'type' => 'box',
            'size_unit' => 'cm',
            'weight_unit' => 'kg',
        ];

        $packagingId = $this->id(
            $this->data(
                $client->post('/packaging', $package),
                'packaging'
            ),
            'packaging_id'
        );

        $pickupId = $this->id(
            $this->data(
                $client->post('/addresses', $context['pickup']),
                'pickup address'
            ),
            'address_id'
        );

        $destination = array_intersect_key(
            $context['destination'],
            array_flip([
                'line1',
                'city',
                'state',
                'country',
                'zip',
            ])
        );

        $destinationId = $this->id(
            $this->data(
                $client->post('/addresses', $destination),
                'delivery address'
            ),
            'address_id'
        );

        $items = array_map(function ($item) {
            unset($item['local_product'], $item['local_variant']);

            return $item;
        }, $context['items']);

        $parcelId = $this->id(
            $this->data(
                $client->post('/parcels', [
                    'description' => 'De Lamoure checkout parcel',
                    'weight_unit' => 'kg',
                    'packaging' => $packagingId,
                    'items' => $items,
                ]),
                'parcel'
            ),
            'parcel_id'
        );

        $raw = $this->data(
            $client->timeout(90)->get('/rates/shipment', [
                'pickup_address' => $pickupId,
                'delivery_address' => $destinationId,
                'parcel_id' => $parcelId,
                'currency' => 'NGN',
            ]),
            'rates'
        );

        $rates = [];

        foreach ($raw as $rate) {
            if (
                ! is_array($rate)
                || ($rate['currency'] ?? '') !== 'NGN'
                || ! is_numeric($rate['amount'] ?? null)
            ) {
                continue;
            }

            $amount = (float) $rate['amount'];
            $rateId = $rate['rate_id'] ?? $rate['id'] ?? '';

            if (
                ! is_string($rateId)
                || $rateId === ''
                || ! is_finite($amount)
                || $amount <= 0
                || $amount > 100000000
            ) {
                continue;
            }

            $description = (string) (
                $rate['carrier_rate_description'] ?? ''
            );

            $baseKobo = (int) round($amount * 100);
            $feeKobo = (int) round($context['extra_fee'] * 100);

            $markupKobo = (int) round(
                ($baseKobo + $feeKobo) * $context['markup'] / 100
            );

            $rates[] = [
                'id' => $rateId,
                'carrier_reference' => (string) (
                    $rate['carrier_reference'] ?? $rate['carrier_id'] ?? ''
                ),
                'carrier' => (string) ($rate['carrier_name'] ?? 'Carrier'),
                'service' => $description,
                'estimate' => (string) ($rate['delivery_time'] ?? ''),
                'base_cost' => ($baseKobo + $feeKobo) / 100,
                'api_amount' => $baseKobo / 100,
                'extra_fee' => $feeKobo / 100,
                'markup_percentage' => $context['markup'],
                'markup_amount' => $markupKobo / 100,
                'total' => ($baseKobo + $feeKobo + $markupKobo) / 100,
            ];
        }

        return [
            'rates' => $rates,
            'parcel_id' => $parcelId,
            'pickup_address_id' => $pickupId,
            'delivery_address_id' => $destinationId,
        ];
    }
}
