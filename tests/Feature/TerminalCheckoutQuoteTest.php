<?php

namespace Tests\Feature;

use App\Http\Controllers\Concerns\HandlesTerminalCheckout;
use App\Models\ShippingPackage;
use App\Models\WebsiteSetting;
use App\Services\TerminalCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TerminalCheckoutQuoteTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $service = app(TerminalCheckoutService::class);
        $key = hash('sha256', 'test-carrier|ground');
        $package = ShippingPackage::create([
            'name' => 'Test box', 'length_cm' => 20, 'width_cm' => 15,
            'height_cm' => 12, 'empty_weight_kg' => 0.15, 'is_active' => true,
        ]);
        $setting = WebsiteSetting::firstOrCreate([]);
        $setting->forceFill([
            'delivery_provider' => 'terminal_africa', 'terminal_environment' => 'live',
            'terminal_checkout_enabled' => true, 'terminal_checkout_confirmed' => true,
            'terminal_checkout_package_id' => $package->id, 'terminal_checkout_max_units' => 3,
            'terminal_checkout_extra_fee' => 0, 'shipping_markup_percentage' => 10,
            'terminal_checkout_services' => json_encode([$key]),
            'terminal_credentials' => ['live' => 'sk_live_fake_test_fixture'],
            'dispatch_city' => 'Ajah', 'dispatch_state' => 'Lagos', 'dispatch_country' => 'NG',
        ])->save();
        $cart = collect([(object) ['name' => 'Diffuser', 'price' => 45000, 'quantity' => 1,
            'attributes' => collect(['product_id' => 1, 'variant_id' => null, 'shipping_weight_kg' => 0.65])]]);
        return [$service, $setting, $cart, $key];
    }

    public function test_markup_and_extra_fee_are_computed_from_provider_amount(): void
    {
        [$service, $setting, $cart] = $this->fixture();
        $setting->terminal_checkout_extra_fee = 300;
        $context = $service->context($setting, $cart, ['city' => 'Ikeja', 'state' => 'Lagos', 'country' => 'NG']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            '*/packaging' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['packaging_id' => 'PA-test']]),
            '*/addresses' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['address_id' => 'AD-test']]),
            '*/parcels' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['parcel_id' => 'PC-test']]),
            '*/rates/shipment*' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => [[
                'rate_id' => 'RT-test', 'currency' => 'NGN', 'amount' => 4000,
                'carrier_reference' => 'test-carrier', 'carrier_rate_description' => 'ground',
            ]]]),
        ]);
        $rate = $service->quote($setting, $context)['rates'][0];
        $this->assertEquals(4300, $rate['base_cost']);
        $this->assertEquals(430, $rate['markup_amount']);
        $this->assertEquals(4730, $rate['total']);
        \Illuminate\Support\Facades\Http::assertSentCount(5);
    }

    public function test_changed_cart_or_weight_changes_quote_fingerprint(): void
    {
        [$service, $setting, $cart] = $this->fixture();
        $destination = ['city' => 'Ikeja', 'state' => 'Lagos', 'country' => 'NG'];
        $old = $service->fingerprint($service->context($setting, $cart, $destination));
        $cart[0]->quantity = 2;
        $this->assertNotSame($old, $service->fingerprint($service->context($setting, $cart, $destination)));
        $cart[0]->quantity = 1;
        $cart[0]->attributes->put('shipping_weight_kg', 0.9);
        $this->assertNotSame($old, $service->fingerprint($service->context($setting, $cart, $destination)));
    }

    public function test_missing_variant_weight_cannot_fall_back_to_fake_weight(): void
    {
        [$service, $setting, $cart] = $this->fixture();
        $cart[0]->attributes->put('shipping_weight_kg', null);
        $this->expectException(ValidationException::class);
        $service->context($setting, $cart, ['city' => 'Ikeja']);
    }

    public function test_forged_sandbox_submission_is_blocked_before_order_processing(): void
    {
        $this->fixture();
        session(['terminal_checkout_preview_until' => time() + 1800]);
        $controller = new class {
            use HandlesTerminalCheckout;
            public function choose(): array { return $this->resolveTerminalSelection([], collect()); }
        };
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Sandbox preview only');
        $controller->choose();
    }

    public function test_unapproved_service_is_never_returned_for_live_checkout(): void
    {
        [$service, $setting, $cart] = $this->fixture();
        $context = $service->context($setting, $cart, ['city' => 'Ikeja', 'state' => 'Lagos', 'country' => 'NG']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            '*/packaging' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['packaging_id' => 'PA-test']]),
            '*/addresses' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['address_id' => 'AD-test']]),
            '*/parcels' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => ['parcel_id' => 'PC-test']]),
            '*/rates/shipment*' => \Illuminate\Support\Facades\Http::response(['status' => true, 'data' => [[
                'rate_id' => 'RT-test', 'currency' => 'NGN', 'amount' => 100,
                'carrier_reference' => 'other-carrier', 'carrier_rate_description' => 'air',
            ]]]),
        ]);
        $this->assertSame([], $service->quote($setting, $context)['rates']);
    }
}
