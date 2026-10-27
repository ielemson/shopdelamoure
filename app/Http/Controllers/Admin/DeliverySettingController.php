<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use App\Services\TerminalCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeliverySettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $packages = ShippingPackage::where('is_active', true)->orderBy('id')->get();
        $carriers = [];
        $carrierError = null;
        try {
            if (!empty(($setting->terminal_credentials ?? [])['live'])) {
                $cacheKey = 'terminal-live-carriers:'.hash('sha256', (string) $setting->terminal_credentials['live']);
                $carriers = Cache::remember($cacheKey, 300, fn() => app(TerminalCheckoutService::class)->carriers($setting));
            }
        } catch (\Throwable $e) { $carrierError = 'Carrier list could not be loaded. Use Test live connection to check access.'; }
        return view('admin.settings.delivery', compact('setting', 'packages', 'carriers', 'carrierError'));
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $isTerminal = $request->input('delivery_provider') === 'terminal_africa';
        $validator = Validator::make($request->all(), [
            'delivery_provider' => ['required', Rule::in(['local', 'terminal_africa'])],
            'terminal_live_secret' => ['nullable', 'string', 'max:512'],
            'shipping_markup_percentage' => ['required', 'numeric', 'between:0,100'],
            'terminal_checkout_extra_fee' => ['nullable', 'numeric', 'between:0,100000'],
            'terminal_checkout_package_id' => ['nullable', 'integer', Rule::exists('shipping_packages', 'id')->where('is_active', true)],
            'terminal_auto_book' => ['sometimes', 'boolean'],
            'dispatch_name' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:150'],
            'dispatch_email' => ['nullable', 'email', 'max:255'],
            'dispatch_phone' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:30'],
            'dispatch_line1' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:255'],
            'dispatch_city' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:100'],
            'dispatch_state' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:100'],
            'dispatch_postal_code' => [Rule::requiredIf($isTerminal), 'nullable', 'string', 'max:20'],
        ]);
        $validator->after(function ($validator) use ($request, $setting, $isTerminal) {
            if ($validator->errors()->any() || !$isTerminal) return;
            $secret = trim((string) $request->input('terminal_live_secret', ''));
            if ($secret === '') $secret = trim((string) (($setting->terminal_credentials ?? [])['live'] ?? ''));
            if ($secret === '' || preg_match('/[\r\n]/', $secret)) $validator->errors()->add('terminal_live_secret', 'Save a valid live secret key.');
            if (!ShippingPackage::where('is_active', true)->exists()) $validator->errors()->add('terminal_checkout_package_id', 'Add an active packaging preset to calculate parcel rates.');
            $name = preg_split('/\s+/', trim((string) $request->input('dispatch_name')), 2);
            if (count($name) < 2) $validator->errors()->add('dispatch_name', 'Enter the sender’s first and last name for pickup booking.');
        });
        if ($validator->fails()) return back()->withErrors($validator)->withInput($request->except([
            'terminal_live_secret', 'terminal_test_secret', '_token', '_method',
        ]));
        $data = $validator->validated();
        $credentials = $setting->terminal_credentials ?? [];
        $secret = trim((string) ($data['terminal_live_secret'] ?? ''));
        if ($secret !== '') $credentials['live'] = $secret;
        unset($data['terminal_live_secret']);
        $data['terminal_checkout_package_id'] = $data['terminal_checkout_package_id'] ?? ShippingPackage::where('is_active', true)->orderBy('id')->value('id');
        $data['terminal_checkout_extra_fee'] = $data['terminal_checkout_extra_fee'] ?? 0;
        $data['terminal_auto_book'] = $request->boolean('terminal_auto_book');
        foreach ($data as $key => $value) $setting->{$key} = $value;
        $setting->terminal_credentials = $credentials ?: null;
        $setting->terminal_environment = 'live';
        $setting->dispatch_country = 'NG';
        // Legacy fields retained for compatibility, not used as activation gates.
        $setting->terminal_checkout_enabled = $isTerminal;
        $setting->save();
        session()->forget(['terminal_checkout_quote', 'terminal_checkout_preview_until',
            'terminal_checkout_preview_package', 'terminal_checkout_seen_services', 'terminal_live_seen_services']);
        return redirect()->route('admin.delivery.settings')->with('success',
            $isTerminal ? 'Terminal Africa live checkout is active.' : 'Settings saved. Local Delivery is active.');
    }

    public function testConnection()
    {
        try {
            $carriers = app(TerminalCheckoutService::class)->carriers(WebsiteSetting::firstOrCreate([]));
            return response()->json(['success' => true, 'message' => 'Live connection successful. '.count($carriers).' active account carrier(s) returned. No shipment was booked.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Live connection failed. Check your saved live secret, account access and connectivity.'], 422);
        }
    }

    // Existing legacy routes remain harmless after deployment.
    public function preview() { return redirect()->route('admin.delivery.settings'); }
    public function endPreview() { return redirect()->route('admin.delivery.settings'); }
    public function discoverLiveServices() { return redirect()->route('admin.delivery.settings'); }
}
