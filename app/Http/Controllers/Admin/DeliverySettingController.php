<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliverySettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate([]);

        return view('admin.settings.delivery', compact('setting'));
    }

    public function update(Request $request)
    {
        // Validate explicitly so API keys never enter flashed old input.
        $validator = Validator::make($request->all(), [
            'delivery_provider' => ['required', Rule::in(['local'])],
            'shipping_markup_percentage' => ['required', 'numeric', 'between:0,100', 'regex:/^\d+(\.\d{1,2})?$/'],
            'terminal_environment' => ['required', Rule::in(['test', 'live'])],
            'terminal_test_secret' => ['nullable', 'string', 'max:512'],
            'terminal_live_secret' => ['nullable', 'string', 'max:512'],
            'dispatch_name' => ['required', 'string', 'max:150'],
            'dispatch_email' => ['required', 'email', 'max:255'],
            'dispatch_phone' => ['nullable', 'string', 'max:30'],
            'dispatch_line1' => ['required', 'string', 'max:255'],
            'dispatch_city' => ['required', 'string', 'max:100'],
            'dispatch_state' => ['required', 'string', 'max:100'],
            'dispatch_country' => ['required', Rule::in(['NG'])],
            'dispatch_postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except([
                'terminal_test_secret', 'terminal_live_secret', '_token', '_method',
            ]));
        }

        $data = $validator->validated();
        $setting = WebsiteSetting::firstOrCreate([]);
        $credentials = $setting->terminal_credentials ?? [];

        foreach (['test', 'live'] as $environment) {
            $secret = trim((string) ($data['terminal_'.$environment.'_secret'] ?? ''));
            if ($secret !== '') {
                $credentials[$environment] = $secret;
            }
            unset($data['terminal_'.$environment.'_secret']);
        }

        $setting->fill($data);
        $setting->terminal_credentials = $credentials ?: null;
        $setting->save();

        return redirect()->route('admin.delivery.settings')
            ->with('success', 'Delivery configuration saved. Local Delivery remains active.');
    }

    public function testConnection()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $environment = $setting->terminal_environment ?? 'test';

        if (! in_array($environment, ['test', 'live'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Save a valid environment first.',
            ], 422);
        }

        try {
            // The model's encrypted:array cast decrypts credentials automatically.
            $credentials = $setting->terminal_credentials ?? [];
            $secret = trim((string) ($credentials[$environment] ?? ''));

            if ($secret === '') {
                return response()->json([
                    'success' => false,
                    'message' => "Save a secret key for the selected {$environment} environment first.",
                ], 422);
            }

            // Fixed provider URLs; redirects are disabled.
            $baseUrl = $environment === 'test'
                ? 'https://sandbox.terminal.africa/v1'
                : 'https://api.terminal.africa/v1';

            $response = Http::withToken($secret)
                ->acceptJson()
                ->connectTimeout(10)
                ->timeout(20)
                ->withOptions(['allow_redirects' => false])
                ->get($baseUrl.'/users/carriers', [
                    'perPage' => 1,
                ]);

            if ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => "Terminal Africa rejected the saved {$environment} secret key (HTTP 401). Re-enter the matching secret key, save settings, then test again.",
                ], 422);
            }

            if ($response->status() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => "Terminal Africa denied access to the carrier list in {$environment} mode (HTTP 403). Check API/account permissions with Terminal Africa.",
                ], 422);
            }

            if (! $response->successful() || $response->json('status') !== true) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal Africa did not confirm the connection (HTTP '
                        .$response->status().'). Please try again later.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'message' => "Connected to Terminal Africa ({$environment}). No shipment was booked. This test does not verify rates or product eligibility.",
            ]);
        } catch (\Throwable $e) {
            // Do not expose credentials, headers, or raw exception details.
            return response()->json([
                'success' => false,
                'message' => 'Connection could not be completed. Check server connectivity and configuration.',
            ], 502);
        }
    }
}
