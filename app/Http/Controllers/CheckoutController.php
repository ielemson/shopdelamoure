<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupLocation;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use App\Models\State;
use App\Services\OrderPaymentService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Checkout Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $cartItems = Cart::getContent();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('shop')
                ->with('error', 'Your cart is empty.');
        }

        $countries = Country::query()
            ->whereRaw('LOWER(name) = ?', ['nigeria'])
            ->get();

        $pickupLocations = PickupLocation::with('state')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $subtotal = (float) Cart::getSubTotal();
        $shipping = 0;
        $vat = 0;

        /*
        |--------------------------------------------------------------------------
        | Coupon Display Amount
        |--------------------------------------------------------------------------
        |
        | This is only for rendering the checkout page.
        | The coupon is independently revalidated and recalculated in store().
        |
        */

        $discount = (float) data_get(
            session('coupon'),
            'discount_amount',
            0
        );

        $discount = max(
            0,
            min($discount, $subtotal)
        );

        $total = $subtotal + $shipping + $vat - $discount;

        $addresses = collect();
        $defaultAddress = null;

        if (auth()->check()) {
            $addresses = auth()->user()
                ->address()
                ->with(['country', 'state'])
                ->latest()
                ->get();

            $defaultAddress = auth()->user()
                ->address()
                ->with(['country', 'state'])
                ->latest()
                ->first();
        }

        return view(
            'frontend.checkout.index',
            compact(
                'cartItems',
                'countries',
                'pickupLocations',
                'subtotal',
                'shipping',
                'vat',
                'discount',
                'total',
                'addresses',
                'defaultAddress'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Load States
    |--------------------------------------------------------------------------
    */

    public function states($country)
    {
        return State::where('country_id', $country)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /*
    |--------------------------------------------------------------------------
    | Load Shipping Zones
    |--------------------------------------------------------------------------
    */

    public function shippingZones($stateId)
    {
        $state = State::find($stateId);

        if (! $state) {
            return response()->json([
                'status' => false,
                'message' => 'The selected state could not be found.',
            ], 404);
        }

        $zones = ShippingRate::with('areas')
            ->where('state_id', $stateId)
            ->where('is_active', true)
            ->orderBy('zone_name')
            ->get()
            ->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'zone_name' => $zone->zone_name,
                    'shipping_cost' => (float) $zone->shipping_cost,
                    'areas' => $zone->areas
                        ->pluck('name')
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        return response()->json($zones);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Selected Shipping Rate
    |--------------------------------------------------------------------------
    */

    public function shippingRate(Request $request)
    {
        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'shipping_rate_id' => ['required', 'integer', 'exists:shipping_rates,id'],
        ]);

        $stateExists = State::where('id', $validated['state_id'])
            ->where('country_id', $validated['country_id'])
            ->exists();

        if (! $stateExists) {
            return response()->json([
                'status' => false,
                'message' => 'The selected state does not belong to the selected country.',
            ], 422);
        }

        $shippingRate = $this->resolveShippingRate(
            (int) $validated['country_id'],
            (int) $validated['state_id'],
            (int) $validated['shipping_rate_id']
        );

        if (! $shippingRate) {
            return response()->json([
                'status' => false,
                'message' => 'The selected delivery zone is currently unavailable.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'shipping_rate_id' => $shippingRate->id,
            'zone_name' => $shippingRate->zone_name,
            'shipping_cost' => (float) $shippingRate->shipping_cost,
            'areas' => $shippingRate->areas
                ->pluck('name')
                ->values()
                ->all(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store / Process Checkout
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = auth()->user();
        $userId = $user?->id;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],

            'delivery_method' => ['required', 'in:shipping,pickup'],

            'country_id' => [
                'nullable',
                'required_if:delivery_method,shipping',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'nullable',
                'required_if:delivery_method,shipping',
                'integer',
                'exists:states,id',
            ],

            'shipping_rate_id' => [
                'nullable',
                'required_if:delivery_method,shipping',
                'integer',
                'exists:shipping_rates,id',
            ],

            'street_address' => [
                'nullable',
                'required_if:delivery_method,shipping',
                'string',
                'max:255',
            ],

            'pickup_location_id' => [
                'nullable',
                'required_if:delivery_method,pickup',
                'integer',
                'exists:pickup_locations,id',
            ],

            'delivery_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:paystack'],
            'terms' => ['required', 'accepted'],
        ]);

        $cartItems = Cart::getContent();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your shopping cart is empty.');
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Fulfilment Method
        |--------------------------------------------------------------------------
        */

        $shippingRate = null;
        $pickupLocation = null;
        $shipping = 0;

        if ($validated['delivery_method'] === 'shipping') {
            $stateExists = State::where('id', $validated['state_id'])
                ->where('country_id', $validated['country_id'])
                ->exists();

            if (! $stateExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'state_id' => 'The selected state does not belong to the selected country.',
                    ]);
            }

            $shippingRate = $this->resolveShippingRate(
                (int) $validated['country_id'],
                (int) $validated['state_id'],
                (int) $validated['shipping_rate_id']
            );

            if (! $shippingRate) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'shipping_rate_id' => 'The selected delivery zone is currently unavailable.',
                    ]);
            }

            $shipping = (float) $shippingRate->shipping_cost;
        } else {
            $pickupLocation = PickupLocation::with('state')
                ->where('is_active', true)
                ->find($validated['pickup_location_id']);

            if (! $pickupLocation) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'pickup_location_id' => 'The selected pickup location is currently unavailable.',
                    ]);
            }

            $shipping = 0;
        }

        $subtotal = (float) Cart::getSubTotal();
        $vat = 0;

        /*
        |--------------------------------------------------------------------------
        | Authoritative Coupon Revalidation
        |--------------------------------------------------------------------------
        |
        | Never trust the discount amount already stored in the browser/session
        | when creating the order. The active coupon is loaded again from the
        | database and its discount is recalculated from the current cart.
        |
        */

        try {
            $couponResult = $this->resolveCheckoutCoupon(
                $cartItems,
                $subtotal
            );
        } catch (\RuntimeException $e) {
            session()->forget('coupon');

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }

        $coupon = $couponResult['coupon'];
        $discount = $couponResult['discount'];

        $total = $subtotal + $shipping + $vat - $discount;

        if ($subtotal <= 0 || $total <= 0) {
            return back()
                ->withInput()
                ->with('error', 'Invalid order amount.');
        }

        try {
            $result = DB::transaction(function () use (
                $validated,
                $cartItems,
                $shippingRate,
                $pickupLocation,
                $subtotal,
                $shipping,
                $vat,
                $discount,
                $total,
                $userId,
                $coupon
            ) {
                $address = null;

                /*
                |--------------------------------------------------------------------------
                | Save Customer Address Only For Shipping
                |--------------------------------------------------------------------------
                */

                if (
                    $userId
                    && $validated['delivery_method'] === 'shipping'
                ) {
                    $address = Address::updateOrCreate(
                        ['user_id' => $userId],
                        [
                            'first_name' => $validated['first_name'],
                            'last_name' => $validated['last_name'],
                            'email' => $validated['email'],
                            'phone' => $validated['phone'],
                            'country_id' => $validated['country_id'],
                            'state_id' => $validated['state_id'],
                            'city' => $shippingRate?->zone_name,
                            'street_address' => $validated['street_address'],
                            'delivery_note' => $validated['delivery_note'] ?? null,
                            'is_default' => true,
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Order Location Snapshot
                |--------------------------------------------------------------------------
                */

                if ($validated['delivery_method'] === 'shipping') {
                    $orderCountryId = (int) $validated['country_id'];
                    $orderStateId = (int) $validated['state_id'];
                    $orderCity = $shippingRate?->zone_name;
                    $orderAddress = $validated['street_address'];
                } else {
                    $orderCountryId = $pickupLocation?->state?->country_id;
                    $orderStateId = $pickupLocation?->state_id;
                    $orderCity = $pickupLocation?->city;
                    $orderAddress = $pickupLocation?->address;
                }

                $order = Order::create([
                    'user_id' => $userId,
                    'address_id' => $address?->id,
                    'order_no' => 'ORD-'.strtoupper(uniqid()),

                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => trim($validated['email']),
                    'phone' => $validated['phone'],

                    'country_id' => $orderCountryId,
                    'state_id' => $orderStateId,
                    'city' => $orderCity,
                    'address' => $orderAddress,
                    'order_note' => $validated['delivery_note'] ?? null,

                    'delivery_method' => $validated['delivery_method'],
                    'pickup_location_id' => $pickupLocation?->id,

                    'currency' => 'NGN',
                    'payment_method' => 'paystack',
                    'payment_status' => 'unpaid',

                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'vat' => $vat,

                    /*
                    |--------------------------------------------------------------------------
                    | Coupon Snapshot
                    |--------------------------------------------------------------------------
                    |
                    | The normal discount field remains the amount deducted from
                    | the order. Coupon-specific fields preserve the promotion
                    | that produced that discount.
                    |
                    */

                    'discount' => $discount,
                    'coupon_id' => $coupon?->id,
                    'coupon_code' => $coupon?->code,
                    'coupon_discount_type' => $coupon?->discount_type,
                    'coupon_discount_value' => $coupon
                        ? (float) $coupon->discount_value
                        : null,
                    'coupon_discount_amount' => $discount,

                    'total' => $total,

                    'status' => 'pending',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Order Items
                |--------------------------------------------------------------------------
                */

                foreach ($cartItems as $item) {
                    $variantId = $item->attributes->get('variant_id');
                    $variant = null;

                    if ($variantId) {
                        $variant = ProductVariant::query()
                            ->where('id', $variantId)
                            ->first();

                        if (! $variant) {
                            throw new \RuntimeException(
                                'The selected product variant could not be found.'
                            );
                        }

                        $productId = $variant->product_id;
                    } else {
                        $productId = $item->attributes->get('product_id')
                            ?: $item->id;
                    }

                    $product = Product::find($productId);

                    if (! $product) {
                        throw new \RuntimeException(
                            'A product in your cart could not be found.'
                        );
                    }

                    $variantName = $item->attributes->get('variant_name');
                    $itemName = $item->name;

                    if ($variantName) {
                        $itemName .= ' - '.$variantName;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'product_variant_id' => $variantId ?: null,

                        'name' => $itemName,
                        'sku' => $variant?->sku
                            ?? $product->sku
                            ?? null,
                        'variant_name' => $variantName ?: null,
                        'variant_options' => $variantName
                            ? ['variant' => $variantName]
                            : null,
                        'image' => $item->attributes->get('image') ?: null,

                        'currency' => 'NGN',
                        'price' => (float) $item->price,
                        'quantity' => (int) $item->quantity,
                        'total' => (float) $item->price * (int) $item->quantity,
                    ]);
                }

                return [
                    'order' => $order,
                    'address' => $address,
                    'shippingRate' => $shippingRate,
                    'pickupLocation' => $pickupLocation,
                ];
            });

            $order = $result['order'];
            $address = $result['address'];
            $shippingRate = $result['shippingRate'];
            $pickupLocation = $result['pickupLocation'];

            /*
            |--------------------------------------------------------------------------
            | Paystack Configuration
            |--------------------------------------------------------------------------
            */

            $secretKey = config('services.paystack.secret_key');
            $paymentUrl = config('services.paystack.payment_url');

            if (! $secretKey || ! $paymentUrl) {
                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Order saved, but the payment gateway is not configured.'
                );
            }

            $reference = 'PSK-'.$order->order_no.'-'.time();

            $order->update([
                'payment_reference' => $reference,
            ]);

            $paystackEmail = trim($validated['email']);

            if (! filter_var($paystackEmail, FILTER_VALIDATE_EMAIL)) {
                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Order saved, but the email address is invalid for payment.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Initialize Paystack
            |--------------------------------------------------------------------------
            */

            try {
                $response = Http::withToken($secretKey)
                    ->acceptJson()
                    ->timeout(30)
                    ->connectTimeout(15)
                    ->post(
                        rtrim($paymentUrl, '/').'/transaction/initialize',
                        [
                            'email' => $paystackEmail,
                            'amount' => (int) round($order->total * 100),
                            'currency' => 'NGN',
                            'reference' => $reference,
                            'callback_url' => route('checkout.paystack.callback'),

                            'metadata' => [
                                'order_id' => $order->id,
                                'order_no' => $order->order_no,
                                'address_id' => $address?->id,
                                'user_id' => $userId,
                                'customer_type' => $userId ? 'registered' : 'guest',

                                'delivery_method' => $validated['delivery_method'],

                                'shipping_rate_id' => $shippingRate?->id,
                                'shipping_zone' => $shippingRate?->zone_name,

                                'pickup_location_id' => $pickupLocation?->id,
                                'pickup_location' => $pickupLocation?->name,

                                'shipping' => $shipping,

                                /*
                                |--------------------------------------------------------------------------
                                | Coupon Metadata
                                |--------------------------------------------------------------------------
                                */

                                'coupon_id' => $order->coupon_id,
                                'coupon_code' => $order->coupon_code,
                                'coupon_discount_amount' => (float) $order->coupon_discount_amount,
                            ],
                        ]
                    );

                if (
                    ! $response->successful()
                    || ! ($response->json('status') ?? false)
                ) {
                    Log::error('Paystack initialization failed', [
                        'order_id' => $order->id,
                        'response' => $response->json(),
                    ]);

                    return $this->redirectAfterOrder(
                        $order,
                        'error',
                        'Order saved, but payment could not be initialized. Please try again.'
                    );
                }

                $authorizationUrl = $response->json('data.authorization_url');

                if (! $authorizationUrl) {
                    Log::error('Paystack authorization URL missing', [
                        'order_id' => $order->id,
                        'response' => $response->json(),
                    ]);

                    return $this->redirectAfterOrder(
                        $order,
                        'error',
                        'Order saved, but the payment page could not be opened.'
                    );
                }

                return redirect()->away($authorizationUrl);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Paystack connection error', [
                    'order_id' => $order->id,
                    'message' => $e->getMessage(),
                ]);

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Order saved, but the payment gateway is currently unreachable. Please try again later.'
                );
            }
        } catch (\Throwable $e) {
            Log::error('Checkout store error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    app()->environment('local')
                        ? $e->getMessage()
                        : 'Something went wrong during checkout. Please try again.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout Process Compatibility
    |--------------------------------------------------------------------------
    */

    public function process(Request $request)
    {
        return $this->store($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Paystack Callback
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | Paystack Callback
    |--------------------------------------------------------------------------
    */

    public function paystackCallback(
        Request $request,
        OrderPaymentService $orderPaymentService
    ) {
        /*
        |--------------------------------------------------------------------------
        | Payment Reference
        |--------------------------------------------------------------------------
        */

        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Payment reference missing.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Paystack Configuration
        |--------------------------------------------------------------------------
        */

        $secretKey =
            config('services.paystack.secret_key');

        $paymentUrl =
            config('services.paystack.payment_url');

        if (! $secretKey || ! $paymentUrl) {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Payment gateway is not configured.'
                );
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Verify Transaction With Paystack
            |--------------------------------------------------------------------------
            */

            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->timeout(30)
                ->connectTimeout(15)
                ->get(
                    rtrim($paymentUrl, '/')
                    .'/transaction/verify/'
                    .urlencode($reference)
                );

            /*
            |--------------------------------------------------------------------------
            | Verification Request Failed
            |--------------------------------------------------------------------------
            */

            if (
                ! $response->successful()
                || ! ($response->json('status') ?? false)
            ) {
                return redirect()
                    ->route('checkout.index')
                    ->with(
                        'error',
                        'Could not verify payment.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Paystack Data
            |--------------------------------------------------------------------------
            */

            $data = $response->json('data');

            if (! is_array($data)) {
                return redirect()
                    ->route('checkout.index')
                    ->with(
                        'error',
                        'Invalid payment verification response.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Find Order
            |--------------------------------------------------------------------------
            */

            $order = Order::where(
                'payment_reference',
                $reference
            )->first();

            if (! $order) {

                Log::error(
                    'Paystack callback order not found',
                    [
                        'reference' => $reference,
                    ]
                );

                return redirect()
                    ->route('checkout.index')
                    ->with(
                        'error',
                        'Order could not be found for this payment.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Reference
            |--------------------------------------------------------------------------
            */

            if (
                ($data['reference'] ?? null)
                !== $order->payment_reference
            ) {

                Log::warning(
                    'Paystack reference mismatch',
                    [
                        'order_id' => $order->id,
                        'expected' => $order->payment_reference,
                        'received' => $data['reference'] ?? null,
                    ]
                );

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Payment reference verification failed.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Currency
            |--------------------------------------------------------------------------
            */

            if (
                strtoupper(
                    $data['currency'] ?? ''
                ) !== 'NGN'
            ) {

                Log::warning(
                    'Paystack currency mismatch',
                    [
                        'order_id' => $order->id,
                        'currency' => $data['currency'] ?? null,
                    ]
                );

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Payment currency verification failed.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verify Amount
            |--------------------------------------------------------------------------
            |
            | Paystack returns amount in kobo.
            |
            */

            $expectedAmount = (int) round(
                (float) $order->total * 100
            );

            $paidAmount = (int) (
                $data['amount'] ?? 0
            );

            if ($paidAmount !== $expectedAmount) {

                Log::warning(
                    'Paystack payment amount mismatch',
                    [
                        'order_id' => $order->id,
                        'expected_amount' => $expectedAmount,
                        'paid_amount' => $paidAmount,
                    ]
                );

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Payment amount verification failed.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Successful Payment
            |--------------------------------------------------------------------------
            */

            if (($data['status'] ?? '') === 'success') {

                /*
                |--------------------------------------------------------------------------
                | Process Payment
                |--------------------------------------------------------------------------
                |
                | OrderPaymentService handles:
                |
                | - Atomic paid status update
                | - Duplicate callback/webhook protection
                | - Inventory deduction
                | - Customer confirmation email
                | - Admin order notification
                |
                */

                $orderPaymentService
                    ->processSuccessfulPayment(
                        $order,
                        $data
                    );

                /*
                |--------------------------------------------------------------------------
                | Refresh Order
                |--------------------------------------------------------------------------
                |
                | The webhook may have processed the payment before
                | the browser callback reached this point.
                |
                */

                $order->refresh();

                /*
                |--------------------------------------------------------------------------
                | Record Coupon Usage
                |--------------------------------------------------------------------------
                |
                | Usage is counted only after the order has been confirmed paid.
                | coupon_usage_counted_at prevents duplicate callback increments.
                |
                */

                $this->recordCouponUsage(
                    $order
                );

                /*
                |--------------------------------------------------------------------------
                | Clear Cart / Coupon Session
                |--------------------------------------------------------------------------
                */

                Cart::clear();

                session()->forget('coupon');

                /*
                |--------------------------------------------------------------------------
                | Registered Customer
                |--------------------------------------------------------------------------
                */

                if ($order->user_id) {

                    return redirect()
                        ->route(
                            'customer.orders.show',
                            $order->id
                        )
                        ->with(
                            'success',
                            'Payment successful. Your order is now being processed.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Guest Customer
                |--------------------------------------------------------------------------
                */

                return redirect()
                    ->route('shop')
                    ->with(
                        'success',
                        'Payment successful. Your order number is '
                        .$order->order_no
                        .'. Please keep this number for your records.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Failed / Unsuccessful Payment
            |--------------------------------------------------------------------------
            |
            | Never downgrade an order that was already marked paid by
            | the webhook or an earlier successful callback.
            |
            */

            $order->refresh();

            if ($order->payment_status !== 'paid') {

                $order->update([
                    'payment_status' => 'failed',

                    'payment_gateway_response' => json_encode($data),
                ]);
            }

            return $this->redirectAfterOrder(
                $order,
                'error',
                'Payment was not successful.'
            );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Verification Error
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Paystack verification error',
                [
                    'reference' => $reference,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Recover Existing Order
            |--------------------------------------------------------------------------
            */

            $order = Order::where(
                'payment_reference',
                $reference
            )->first();

            if ($order) {

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Unable to verify payment at this time.'
                );
            }

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Unable to verify payment at this time.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve / Revalidate Checkout Coupon
    |--------------------------------------------------------------------------
    */

    private function resolveCheckoutCoupon(
        $cartItems,
        float $subtotal
    ): array {
        $sessionCoupon = session('coupon');

        /*
        |--------------------------------------------------------------------------
        | No Coupon Applied
        |--------------------------------------------------------------------------
        */

        if (
            ! is_array($sessionCoupon)
            || empty($sessionCoupon['id'])
        ) {
            return [
                'coupon' => null,
                'discount' => 0.0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Load Coupon Fresh From Database
        |--------------------------------------------------------------------------
        */

        $coupon = \App\Models\Coupon::query()
            ->find($sessionCoupon['id']);

        if (! $coupon) {
            throw new \RuntimeException(
                'The coupon applied to your checkout is no longer available.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Session Coupon Identity
        |--------------------------------------------------------------------------
        */

        $sessionCode = strtoupper(
            trim(
                (string) ($sessionCoupon['code'] ?? '')
            )
        );

        if (
            $sessionCode === ''
            || $sessionCode !== strtoupper($coupon->code)
        ) {
            throw new \RuntimeException(
                'The coupon applied to your checkout could not be verified.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Active / Validity
        |--------------------------------------------------------------------------
        */

        if (! $coupon->is_active) {
            throw new \RuntimeException(
                'This coupon is currently inactive.'
            );
        }

        if (
            $coupon->starts_at
            && now()->lt($coupon->starts_at)
        ) {
            throw new \RuntimeException(
                'This coupon is not yet available.'
            );
        }

        if (
            $coupon->expires_at
            && now()->gt($coupon->expires_at)
        ) {
            throw new \RuntimeException(
                'This coupon has expired.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Global Usage Limit
        |--------------------------------------------------------------------------
        |
        | No customer/email history lookup is performed.
        |
        */

        if (
            $coupon->usage_limit !== null
            && $coupon->usage_count >= $coupon->usage_limit
        ) {
            throw new \RuntimeException(
                'This coupon has reached its usage limit.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Order
        |--------------------------------------------------------------------------
        */

        if (
            (float) $coupon->minimum_order_amount > 0
            && $subtotal < (float) $coupon->minimum_order_amount
        ) {
            throw new \RuntimeException(
                'Your order does not meet the minimum amount required for this coupon.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Eligible Merchandise
        |--------------------------------------------------------------------------
        */

        if ($coupon->scope === 'general') {

            $eligibleSubtotal = $subtotal;

        } elseif ($coupon->scope === 'product') {

            $eligibleSubtotal = 0.0;

            foreach ($cartItems as $item) {

                $productId =
                    $this->resolveCartItemProductId(
                        $item
                    );

                if (
                    $productId
                    && (int) $productId ===
                        (int) $coupon->product_id
                ) {
                    $eligibleSubtotal +=
                        (float) $item->price
                        * (int) $item->quantity;
                }
            }

            if ($eligibleSubtotal <= 0) {
                throw new \RuntimeException(
                    'This coupon does not apply to any product in your cart.'
                );
            }

        } else {

            throw new \RuntimeException(
                'This coupon has an invalid scope configuration.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Discount
        |--------------------------------------------------------------------------
        */

        if ($coupon->discount_type === 'percentage') {

            $discount =
                $eligibleSubtotal
                * ((float) $coupon->discount_value / 100);

            if (
                $coupon->maximum_discount_amount !== null
                && $discount >
                    (float) $coupon->maximum_discount_amount
            ) {
                $discount =
                    (float) $coupon->maximum_discount_amount;
            }

        } elseif ($coupon->discount_type === 'fixed') {

            $discount =
                (float) $coupon->discount_value;

        } else {

            throw new \RuntimeException(
                'This coupon has an invalid discount configuration.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Safety Cap
        |--------------------------------------------------------------------------
        */

        $discount = min(
            $discount,
            $eligibleSubtotal,
            $subtotal
        );

        $discount = round(
            max(0, $discount),
            2
        );

        return [
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Product ID From Cart Row
    |--------------------------------------------------------------------------
    |
    | The cart may use a variant row ID instead of the real product ID.
    | product_id is preferred, then variant_id is resolved, and finally
    | the cart row ID is used as a fallback.
    |
    */

    private function resolveCartItemProductId(
        $item
    ): ?int {
        $productId =
            $item->attributes->get(
                'product_id'
            );

        if ($productId) {
            return (int) $productId;
        }

        $variantId =
            $item->attributes->get(
                'variant_id'
            );

        if ($variantId) {
            $variantProductId =
                ProductVariant::query()
                    ->where(
                        'id',
                        $variantId
                    )
                    ->value(
                        'product_id'
                    );

            if ($variantProductId) {
                return (int) $variantProductId;
            }
        }

        return is_numeric($item->id)
            ? (int) $item->id
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Record Paid Coupon Usage
    |--------------------------------------------------------------------------
    |
    | There is deliberately no per-customer/email usage lookup.
    | We maintain only the coupon's global usage_count.
    |
    | coupon_usage_counted_at on orders makes this safe against duplicate
    | browser callbacks.
    |
    */

    private function recordCouponUsage(
        Order $order
    ): void {
        if (
            ! $order->coupon_id
            || $order->payment_status !== 'paid'
        ) {
            return;
        }

        DB::transaction(function () use ($order) {

            $lockedOrder =
                Order::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $order->id
                    );

            if (
                ! $lockedOrder->coupon_id
                || $lockedOrder->payment_status !== 'paid'
                || $lockedOrder->coupon_usage_counted_at
            ) {
                return;
            }

            $coupon =
                \App\Models\Coupon::query()
                    ->lockForUpdate()
                    ->find(
                        $lockedOrder->coupon_id
                    );

            if ($coupon) {
                $coupon->increment(
                    'usage_count'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Mark Usage As Counted
            |--------------------------------------------------------------------------
            */

            $lockedOrder->forceFill([
                'coupon_usage_counted_at' => now(),
            ])->save();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Authoritative Shipping Rate
    |--------------------------------------------------------------------------
    */

    private function resolveShippingRate(
        int $countryId,
        int $stateId,
        int $shippingRateId
    ): ?ShippingRate {
        return ShippingRate::with('areas')
            ->where('id', $shippingRateId)
            ->where('country_id', $countryId)
            ->where('state_id', $stateId)
            ->where('is_active', true)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect After Order Creation
    |--------------------------------------------------------------------------
    */

    private function redirectAfterOrder(
        Order $order,
        string $type,
        string $message
    ) {
        if ($order->user_id) {
            return redirect()
                ->route('customer.orders.show', $order->id)
                ->with($type, $message);
        }

        return redirect()
            ->route('shop')
            ->with(
                $type,
                $message
                .' Your order number is '
                .$order->order_no
                .'.'
            );
    }
}
