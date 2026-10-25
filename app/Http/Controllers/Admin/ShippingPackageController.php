<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ShippingPackageController extends Controller
{
    public function index()
    {
        $packages = ShippingPackage::orderBy('name')->get();

        return view('admin.settings.packaging', compact('packages'));
    }

    private function packageData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'length_cm' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'width_cm' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'height_cm' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'empty_weight_kg' => ['required', 'numeric', 'decimal:0,3', 'min:0.001', 'max:9999999.999'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    public function store(Request $request)
    {
        ShippingPackage::create($this->packageData($request));

        return redirect()->route('admin.shipping-packages.index')->with('success', 'Packaging preset saved.');
    }

    public function update(Request $request, ShippingPackage $shippingPackage)
    {
        $shippingPackage->update($this->packageData($request));

        return redirect()->route('admin.shipping-packages.index')->with('success', 'Packaging preset updated.');
    }

    public function cities()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $stage = 'city lookup';
        try {
            $key = ($setting->terminal_credentials ?? [])['test'] ?? null;
            if (! $key) {
                return response()->json(['success' => false, 'message' => 'Save the test secret first.'], 422);
            }
            $client = Http::withToken($key)->acceptJson()
                ->connectTimeout(10)->timeout(30)
                ->withOptions(['allow_redirects' => false]);
            $base = 'https://sandbox.terminal.africa/v1';
            $states = $this->apiData($client->get($base.'/states', ['country_code' => 'NG']), $stage);
            $state = collect($states)->first(function ($state) {
                return is_array($state)
                    && preg_match('/^Lagos(?: State)?$/i', trim((string) ($state['name'] ?? '')));
            });
            $code = $state['isoCode'] ?? null;
            if (! is_string($code) || $code === '') {
                return response()->json(['success' => false, 'message' => 'Lagos was not found in the provider state list.'], 422);
            }
            $cities = $this->apiData($client->get($base.'/cities', [
                'country_code' => 'NG', 'state_code' => $code,
            ]), $stage);
            $names = collect($cities)->filter(fn ($city) => is_array($city) && is_string($city['name'] ?? null))
                ->pluck('name')->unique()->sort()->values();

            return response()->json([
                'success' => true, 'state' => $state['name'],
                'state_code' => $code, 'accepted_cities' => $names,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Unable to retrieve the sandbox Lagos city list.'], 502);
        }
    }

    public function quote(Request $request)
    {
        $input = $request->validate([
            'package_id' => ['required', 'integer', 'exists:shipping_packages,id'],
            'item_description' => ['required', 'string', 'max:255'],
            'item_weight_kg' => ['required', 'numeric', 'decimal:0,3', 'min:0.001', 'max:9999999.999'],
            'item_value_ngn' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:999999999.99'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
        ]);

        $package = ShippingPackage::where('is_active', true)->findOrFail($input['package_id']);
        $setting = WebsiteSetting::firstOrCreate([]);
        if (($setting->terminal_environment ?? 'test') !== 'test') {
            throw ValidationException::withMessages(['terminal' => 'Select and save Test / Sandbox in Delivery Settings. This diagnostic only runs in sandbox.']);
        }
        if (! $setting->dispatch_city || ! $setting->dispatch_state || $setting->dispatch_country !== 'NG') {
            throw ValidationException::withMessages(['terminal' => 'Save the dispatch city, state and country in Delivery Settings first.']);
        }

        $stage = 'credentials';
        try {
            $key = ($setting->terminal_credentials ?? [])['test'] ?? null;
            if (! $key) {
                return response()->json(['success' => false, 'message' => 'Save a Terminal Africa test secret first.'], 422);
            }
            $client = Http::withToken($key)->acceptJson()->asJson()
                ->connectTimeout(10)->timeout(30)
                ->withOptions(['allow_redirects' => false]);
            $base = 'https://sandbox.terminal.africa/v1';

            // No booking calls. These requests create sandbox packaging/address/parcel records.
            $stage = 'packaging';
            $data = $this->apiData($client->post($base.'/packaging', [
                'name' => $package->name, 'type' => 'box',
                'length' => (float) $package->length_cm,
                'width' => (float) $package->width_cm,
                'height' => (float) $package->height_cm,
                'weight' => (float) $package->empty_weight_kg,
                'size_unit' => 'cm', 'weight_unit' => 'kg',
            ]), $stage);
            $packagingId = $this->identifier($data, 'packaging_id');

            $stage = 'pickup address';
            $pickup = ['city' => $setting->dispatch_city, 'state' => $setting->dispatch_state, 'country' => 'NG'];
            if ($setting->dispatch_postal_code) {
                $pickup['zip'] = $setting->dispatch_postal_code;
            }
            $pickupId = $this->identifier($this->apiData($client->post($base.'/addresses', $pickup), $stage), 'address_id');

            $stage = 'delivery address';
            $destination = ['city' => $input['city'], 'state' => $input['state'], 'country' => 'NG'];
            if (! empty($input['zip'])) {
                $destination['zip'] = $input['zip'];
            }
            $destinationId = $this->identifier($this->apiData($client->post($base.'/addresses', $destination), $stage), 'address_id');

            $stage = 'parcel';
            $parcelId = $this->identifier($this->apiData($client->post($base.'/parcels', [
                'description' => $input['item_description'],
                'weight_unit' => 'kg', 'packaging' => $packagingId,
                'items' => [[
                    'name' => $input['item_description'], 'description' => $input['item_description'],
                    'type' => 'parcel', 'currency' => 'NGN',
                    'value' => (float) $input['item_value_ngn'], 'quantity' => 1,
                    'weight' => (float) $input['item_weight_kg'],
                ]],
            ]), $stage), 'parcel_id');

            $stage = 'rates';
            $rates = $this->apiData($client->timeout(90)->get($base.'/rates/shipment', [
                'pickup_address' => $pickupId, 'delivery_address' => $destinationId,
                'parcel_id' => $parcelId, 'currency' => 'NGN',
            ]), $stage);

            $percentage = (float) ($setting->shipping_markup_percentage ?? 10);
            if (! is_finite($percentage) || $percentage < 0 || $percentage > 100) {
                throw new \RuntimeException('Invalid saved markup.');
            }
            $preview = [];
            foreach ($rates as $rate) {
                if (! is_array($rate) || ($rate['currency'] ?? '') !== 'NGN'
                    || ! is_numeric($rate['amount'] ?? null)) {
                    continue;
                }
                $amount = (float) $rate['amount'];
                if (! is_finite($amount) || $amount <= 0) {
                    continue;
                }
                $quoted = round($amount, 2);
                $markup = round($quoted * $percentage / 100, 2);
                $preview[] = [
                    'carrier' => (string) ($rate['carrier_name'] ?? 'Carrier'),
                    'service' => (string) ($rate['carrier_rate_description'] ?? ''),
                    'delivery_time' => (string) ($rate['delivery_time'] ?? ''),
                    'quoted_amount' => $quoted, 'markup' => $markup,
                    'customer_preview' => round($quoted + $markup, 2),
                ];
            }

            return response()->json([
                'success' => true, 'rates' => $preview, 'markup_percentage' => $percentage,
                'estimated_total_weight_kg' => round((float) $input['item_weight_kg'] + (float) $package->empty_weight_kg, 3),
                'message' => count($preview) ? 'Sandbox quote preview only. No shipment booked.' : 'The API returned no usable NGN rates for these details.',
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $curlCode = null;

            if (preg_match('/cURL error (\d+)/i', $e->getMessage(), $matches)) {
                $curlCode = (int) $matches[1];
            }

            $reason = match ($curlCode) {
                6 => 'DNS lookup failed',
                7 => 'Could not connect to the API',
                28 => 'The API request timed out',
                35 => 'TLS connection failed',
                60 => 'SSL certificate verification failed',
                default => 'The connection could not be completed',
            };

            return response()->json([
                'success' => false,
                'message' => $reason.' at '.$stage
                    .($curlCode !== null ? ' (cURL '.$curlCode.')' : '')
                    .'. No shipment was booked.',
            ], 502);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed at '.$stage.'. Your server could not complete the sandbox request (network, DNS, TLS or timeout). No shipment was booked.',
            ], 502);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'The saved test key could not be decrypted. Re-enter and save it in Delivery Settings. Do not change APP_KEY.',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application error at '.$stage.'. The request could not be completed. No shipment was booked.',
            ], 502);
        }
    }

    private function apiData($response, string $stage): array
    {
        $body = $response->json();
        $status = $response->status();
        if (! $response->successful() || ! is_array($body) || ($body['status'] ?? null) !== true) {
            $message = is_array($body) ? ($body['message'] ?? null) : null;
            if (! is_string($message) || trim($message) === '') {
                $message = is_array($body)
                    ? 'The API did not confirm success.'
                    : 'The server returned a non-JSON response.';
            }
            // Do not attach the request, headers, complete response or error traces.
            throw new \UnexpectedValueException('HTTP '.$status.' — '.mb_substr($message, 0, 2000));
        }
        if (! is_array($body['data'] ?? null)) {
            throw new \UnexpectedValueException('HTTP '.$status.' — Success response has an unexpected data format.');
        }

        return $body['data'];
    }

    private function identifier(array $data, string $field): string
    {
        $id = $data[$field] ?? $data['id'] ?? null;
        if (! is_string($id) || $id === '') {
            throw new \UnexpectedValueException('The API response is missing '.$field.' and id.');
        }

        return $id;
    }
}
