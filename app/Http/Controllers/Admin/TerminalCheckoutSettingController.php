<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use App\Services\TerminalCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TerminalCheckoutSettingController extends Controller
{
    public function edit(TerminalCheckoutService $service)
    {
        $setting = WebsiteSetting::firstOrCreate([]);
        $packages = ShippingPackage::where('is_active', true)->orderBy('name')->get();
        $allowed = $service->allowed($setting);
        $services = session('terminal_checkout_seen_services', []);
        foreach ($allowed as $key) $services[$key] ??= 'Previously approved service ('.$key.')';
        return view('admin.settings.terminal-checkout', compact('setting', 'packages', 'allowed', 'services'));
    }

    public function preview(Request $request)
    {
        $data = $request->validate(['package_id' => ['required', 'integer', 'exists:shipping_packages,id']]);
        abort_unless(ShippingPackage::where('is_active', true)->whereKey($data['package_id'])->exists(), 422);
        session()->forget('terminal_checkout_quote');
        session([
            'terminal_checkout_preview_until' => time() + 1800,
            'terminal_checkout_preview_package' => (int) $data['package_id'],
        ]);
        return redirect()->route('checkout.index');
    }

    public function stop()
    {
        session()->forget(['terminal_checkout_preview_until', 'terminal_checkout_preview_package', 'terminal_checkout_quote']);
        return redirect()->route('admin.terminal-checkout.edit')->with('success', 'Sandbox checkout preview ended.');
    }

    public function update(Request $request, TerminalCheckoutService $service)
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
            'package_id' => ['required', 'integer', 'exists:shipping_packages,id'],
            'max_units' => ['required', 'integer', 'between:1,50'],
            'extra_fee' => ['required', 'numeric', 'decimal:0,2', 'between:0,100000'],
            'markup' => ['required', 'numeric', 'decimal:0,2', 'between:0,100'],
            'services' => ['nullable', 'array', 'max:100'],
            'services.*' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
            'confirmed' => ['required', 'boolean'],
        ]);
        $setting = WebsiteSetting::firstOrCreate([]);
        $known = array_unique(array_merge(array_keys(session('terminal_checkout_seen_services', [])), $service->allowed($setting)));
        $selected = array_values(array_unique($data['services'] ?? []));
        if (array_diff($selected, $known)) {
            throw ValidationException::withMessages(['services' => 'Select services returned by your sandbox checkout preview.']);
        }
        abort_unless(ShippingPackage::where('is_active', true)->whereKey($data['package_id'])->exists(), 422);
        if ($data['enabled'] && (!$data['confirmed'] || !$selected || $setting->terminal_environment !== 'live'
            || !str_starts_with((string) (($setting->terminal_credentials ?? [])['live'] ?? ''), 'sk_live_'))) {
            throw ValidationException::withMessages(['enabled' => 'Confirm packaging, fees and service eligibility, select an approved service, and save Live environment with a live secret before activation.']);
        }
        $setting->forceFill([
            'terminal_checkout_enabled' => (bool) $data['enabled'],
            'terminal_checkout_package_id' => $data['package_id'],
            'terminal_checkout_max_units' => $data['max_units'],
            'terminal_checkout_extra_fee' => $data['extra_fee'],
            'shipping_markup_percentage' => $data['markup'],
            'terminal_checkout_services' => json_encode($selected, JSON_THROW_ON_ERROR),
            'terminal_checkout_confirmed' => (bool) $data['confirmed'],
            'delivery_provider' => $data['enabled'] ? 'terminal_africa' : 'local',
        ])->save();
        session()->forget('terminal_checkout_quote');
        return back()->with('success', $data['enabled'] ? 'Terminal Africa checkout enabled. Book paid orders manually; automatic booking is not connected.' : 'Settings saved. Local Delivery is active.');
    }
}
