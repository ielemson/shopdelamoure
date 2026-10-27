<?php
namespace App\Http\Controllers\Concerns;

use App\Models\Country;
use App\Models\State;
use App\Models\WebsiteSetting;
use App\Services\TerminalCheckoutService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait HandlesTerminalCheckout
{
    private function terminalDestination(array $input): array
    {
        $country = Country::find($input['country_id']);
        $state = State::where('country_id', $input['country_id'])->find($input['state_id']);
        if (!$country || mb_strtolower(trim($country->name)) !== 'nigeria' || !$state) {
            throw ValidationException::withMessages(['state_id' => 'Choose a valid Nigerian state.']);
        }
        return ['country' => 'NG', 'state' => $state->name, 'city' => trim($input['terminal_city']),
            'zip' => trim((string) ($input['terminal_zip'] ?? '')),
            'country_id' => (int) $country->id, 'state_id' => (int) $state->id,
            'line1' => trim($input['street_address'])];
    }

    public function terminalCities(Request $request, TerminalCheckoutService $service)
    {
        $input = $request->validate(['state_id' => ['required', 'integer', 'exists:states,id']]);
        $setting = WebsiteSetting::firstOrCreate([]);
        abort_unless($service->available($setting), 409, 'Terminal checkout is unavailable.');
        $state = State::with('country')->findOrFail($input['state_id']);
        abort_unless(mb_strtolower(trim($state->country?->name ?? '')) === 'nigeria', 422);
        try {
            return response()->json(['cities' => $service->cities($setting, $state->name)]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['message' => 'Unable to load delivery cities. Please try again.'], 502);
        }
    }

    public function terminalRates(Request $request, TerminalCheckoutService $service)
    {
        $input = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'terminal_city' => ['required', 'string', 'max:100'],
            'terminal_zip' => ['nullable', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
        ]);
        session()->forget('terminal_checkout_quote');
        $setting = WebsiteSetting::firstOrCreate([]);
        $destination = $this->terminalDestination($input);
        if (!$service->preview() && $destination['zip'] === '') {
            throw ValidationException::withMessages(['terminal_zip' => 'Enter the delivery postal code.']);
        }
        try {
            $prepared = $this->prepareCheckoutCart(Cart::getContent());
            $context = $service->context($setting, $prepared['cartItems'], $destination);
            if (!in_array($destination['city'], $service->cities($setting, $destination['state']), true)) {
                throw ValidationException::withMessages(['terminal_city' => 'Select a city from the available list.']);
            }
            $result = $service->quote($setting, $context);
            $token = bin2hex(random_bytes(24));
            session(['terminal_checkout_quote' => $result + [
                'token' => $token, 'fingerprint' => $service->fingerprint($context),
                'expires_at' => time() + 300, 'environment' => $context['environment'],
                'pickup_snapshot' => $context['pickup'],
            ]]);
            return response()->json(['rates' => $result['rates'], 'quote_token' => $token,
                'expires_in' => 300, 'sandbox' => $context['environment'] === 'test']);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['message' => 'Delivery rates could not be loaded. Please try again.'], 502);
        }
    }

    private function resolveTerminalSelection(array $input, $cart): array
    {
        $service = app(TerminalCheckoutService::class);
        $setting = WebsiteSetting::firstOrCreate([]);
        if ($service->preview() || $setting->terminal_environment !== 'live') {
            throw ValidationException::withMessages(['terminal_shipping' => 'End sandbox preview before placing a live order.']);
        }
        $context = $service->context($setting, $cart, $this->terminalDestination($input));
        $quote = session('terminal_checkout_quote', []);
        if (($quote['environment'] ?? '') !== 'live' || ($quote['expires_at'] ?? 0) <= time()
            || !hash_equals((string) ($quote['token'] ?? ''), (string) ($input['terminal_quote_token'] ?? ''))
            || !hash_equals((string) ($quote['fingerprint'] ?? ''), $service->fingerprint($context))) {
            throw ValidationException::withMessages(['terminal_shipping' => 'Your cart, address or settings changed, or the quote expired. Request rates again.']);
        }
        $rate = collect($quote['rates'] ?? [])->firstWhere('id', $input['terminal_rate_id'] ?? '');
        if (!$rate || !in_array($rate['service_key'], $service->allowed($setting), true)) {
            throw ValidationException::withMessages(['terminal_shipping' => 'Select an available delivery service.']);
        }
        return $rate + ['parcel_id' => $quote['parcel_id'], 'pickup_address_id' => $quote['pickup_address_id'],
            'delivery_address_id' => $quote['delivery_address_id'], 'environment' => 'live',
            'pickup_snapshot' => $context['pickup'], 'destination_snapshot' => $context['destination'],
            'city' => $context['destination']['city'], 'zip' => $context['destination']['zip'],
            'quoted_at' => date(DATE_ATOM, $quote['expires_at'] - 300)];
    }
}
