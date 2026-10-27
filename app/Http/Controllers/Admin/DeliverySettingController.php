<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use App\Services\TerminalCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliverySettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $packages = ShippingPackage::where('is_active', true)->orderBy('name')->get();
        $approvedServices = app(TerminalCheckoutService::class)->allowed($setting);
        $labels = json_decode((string) $setting->terminal_checkout_service_labels, true) ?: [];
        $serviceChoices = array_merge(array_fill_keys($approvedServices, 'Previously approved service'), $labels,
            (array) session('terminal_checkout_seen_services', []));
        return view('admin.settings.delivery', compact('setting', 'packages', 'approvedServices', 'serviceChoices'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_provider' => ['required', Rule::in(['local', 'terminal_africa'])],
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
            'terminal_checkout_package_id' => ['nullable', 'integer', Rule::exists('shipping_packages', 'id')->where('is_active', true)],
            'terminal_checkout_max_units' => ['required', 'integer', 'between:1,100'],
            'terminal_checkout_extra_fee' => ['required', 'numeric', 'between:0,100000'],
            'terminal_checkout_services' => ['nullable', 'array', 'max:100'],
            'terminal_checkout_services.*' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/', 'distinct'],
            'terminal_checkout_confirmed' => ['required', 'boolean'],
            'terminal_auto_book' => ['required', 'boolean'],
        ]);
        $setting = WebsiteSetting::firstOrCreate([]);
        $validator->after(function ($validator) use ($request, $setting) {
            if ($validator->errors()->any()) return;
            $known = array_merge(app(TerminalCheckoutService::class)->allowed($setting),
                array_keys((array) session('terminal_checkout_seen_services', [])));
            $selected = $request->input('terminal_checkout_services', []);
            if (is_array($selected) && array_diff($selected, $known)) {
                $validator->errors()->add('terminal_checkout_services', 'Select services observed in your sandbox preview.');
            }
            if ($request->input('delivery_provider') !== 'terminal_africa') return;
            foreach (['terminal_checkout_package_id', 'dispatch_phone', 'dispatch_postal_code'] as $field) {
                if (!$request->filled($field)) $validator->errors()->add($field, 'Complete this field before live activation.');
            }
            if ($request->input('terminal_environment') !== 'live') $validator->errors()->add('terminal_environment', 'Select Live for live checkout.');
            if (!$request->boolean('terminal_checkout_confirmed')) $validator->errors()->add('terminal_checkout_confirmed', 'Confirm packaging, eligible services and fees first.');
            if (!is_array($selected) || !$selected) $validator->errors()->add('terminal_checkout_services', 'Select at least one approved service.');
            $secret = trim((string) $request->input('terminal_live_secret', ''));
            $secret = $secret !== '' ? $secret : trim((string) (($setting->terminal_credentials ?? [])['live'] ?? ''));
            if ($secret === '') $validator->errors()->add('terminal_live_secret', 'Save a live secret first.');
        });
        if ($validator->fails()) return back()->withErrors($validator)->withInput($request->except([
            'terminal_test_secret', 'terminal_live_secret', '_token', '_method',
        ]));
        $data = $validator->validated();
        $credentials = $setting->terminal_credentials ?? [];
        foreach (['test', 'live'] as $environment) {
            $secret = trim((string) ($data['terminal_'.$environment.'_secret'] ?? ''));
            if ($secret !== '') $credentials[$environment] = $secret;
            unset($data['terminal_'.$environment.'_secret']);
        }
        $services = array_values($data['terminal_checkout_services'] ?? []);
        $labels = array_merge(json_decode((string) $setting->terminal_checkout_service_labels, true) ?: [],
            (array) session('terminal_checkout_seen_services', []));
        $data['terminal_checkout_services'] = json_encode($services, JSON_THROW_ON_ERROR);
        $data['terminal_checkout_service_labels'] = json_encode(array_intersect_key($labels, array_flip($services)), JSON_THROW_ON_ERROR);
        $data['terminal_checkout_enabled'] = $data['delivery_provider'] === 'terminal_africa';
        // Explicit assignments support the existing model without expanding mass assignment.
        foreach ($data as $key => $value) $setting->{$key} = $value;
        $setting->terminal_credentials = $credentials ?: null;
        $setting->save();
        session()->forget('terminal_checkout_quote');
        if ($setting->delivery_provider === 'terminal_africa') session()->forget(['terminal_checkout_preview_until', 'terminal_checkout_preview_package']);
        return redirect()->route('admin.delivery.settings')->with('success',
            $setting->delivery_provider === 'terminal_africa' ? 'Terminal Africa live checkout activated.' : 'Settings saved. Local Delivery is active.');
    }

    public function preview(Request $request)
    {
        $data = $request->validate(['package_id' => ['required', 'integer', Rule::exists('shipping_packages', 'id')->where('is_active', true)]]);
        session(['terminal_checkout_preview_until' => time() + 1800, 'terminal_checkout_preview_package' => $data['package_id']]);
        session()->forget('terminal_checkout_quote');
        return redirect()->to(url('/checkout'))->with('success', 'Sandbox preview started for this browser. Use one item, quantity 1.');
    }

    public function endPreview()
    {
        session()->forget(['terminal_checkout_preview_until', 'terminal_checkout_preview_package', 'terminal_checkout_quote']);
        return back()->with('success', 'Sandbox preview ended.');
    }

    public function testConnection()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $environment = $setting->terminal_environment ?? 'test';
        if (!in_array($environment, ['test', 'live'], true)) return response()->json(['success' => false, 'message' => 'Save a valid environment first.'], 422);
        try {
            $secret = trim((string) (($setting->terminal_credentials ?? [])[$environment] ?? ''));
            if ($secret === '') return response()->json(['success' => false, 'message' => 'Save a secret for this environment first.'], 422);
            $url = $environment === 'test' ? 'https://sandbox.terminal.africa/v1' : 'https://api.terminal.africa/v1';
            $response = Http::withToken($secret)->acceptJson()->connectTimeout(10)->timeout(20)
                ->withOptions(['allow_redirects' => false])->get($url.'/users/carriers', ['perPage' => 1]);
            if (!$response->successful() || $response->json('status') !== true) return response()->json([
                'success' => false, 'message' => 'Terminal Africa connection failed (HTTP '.$response->status().'). Check the saved key and account access.',
            ], 422);
            return response()->json(['success' => true, 'message' => "Connected to Terminal Africa ({$environment}). No shipment was booked."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Connection could not be completed. Check connectivity and saved settings.'], 502);
        }
    }
}
