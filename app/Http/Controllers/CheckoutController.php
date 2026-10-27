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
use App\Models\WebsiteSetting;
use App\Services\OrderPaymentService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesTerminalCheckout;
    private const MAX_QUANTITY = 4294967295;

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
        try {
            $prepared = $this->prepareCheckoutCart($cartItems);
            foreach ($prepared['cartItems'] as $item) {
                Cart::update($item->id, [
                    'price' => $item->price,
                    'attributes' => $item->attributes->toArray(),
                ]);
            }
            session(['currency' => 'NGN']);
            $cartItems = Cart::getContent();
            if ($cartItems->contains(fn ($item) => $item->attributes->get('purchase_type') === 'wholesale')) {
                session()->forget('coupon');
            }
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->with('error', collect($e->errors())->flatten()->first());
        }

        $countries = Country::query()
            ->whereRaw('LOWER(name) = ?', ['nigeria'])
            ->get();
        $pickupLocations = PickupLocation::with('state')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
        $subtotal = (float) Cart::getSubTotal(false);
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
        $terminalService = app(\App\Services\TerminalCheckoutService::class);
        $deliverySetting = WebsiteSetting::firstOrCreate([]);
        $terminalMode = $terminalService->available($deliverySetting);
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
                \Illuminate\Validation\Rule::requiredIf(fn () => $request->input('delivery_method') === 'shipping' && !$terminalMode),
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
            'terminal_city' => ['nullable', 'string', 'max:100', \Illuminate\Validation\Rule::requiredIf(fn () => $terminalMode && $request->input('delivery_method') === 'shipping')],
            'terminal_zip' => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::requiredIf(fn () => $terminalMode && $request->input('delivery_method') === 'shipping')],
            'terminal_rate_id' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::requiredIf(fn () => $terminalMode && $request->input('delivery_method') === 'shipping')],
            'terminal_quote_token' => ['nullable', 'string', 'max:100', \Illuminate\Validation\Rule::requiredIf(fn () => $terminalMode && $request->input('delivery_method') === 'shipping')],
            'delivery_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:paystack'],
            'terms' => ['required', 'accepted'],
        ]);

        $deliverySetting = WebsiteSetting::firstOrCreate([]);

        $shippingProvider = $validated['delivery_method'] === 'pickup'
            ? 'pickup'
            : ($terminalMode ? 'terminal_africa' : ($deliverySetting->delivery_provider ?? 'local'));

        // Enforce the pickup setting on the server as well as the page.
        if (
            $validated['delivery_method'] === 'pickup'
            && ! $deliverySetting->enable_store_pickup
        ) {
            throw ValidationException::withMessages([
                'delivery_method' => 'Store pickup is currently unavailable.',
            ]);
        }

        // Prevent unfinished providers from using local rates accidentally.
        if (
            $validated['delivery_method'] === 'shipping'
            && !in_array($shippingProvider, ['local', 'terminal_africa'], true)
        ) {
            throw ValidationException::withMessages([
                'delivery_method' => 'The selected delivery service is currently unavailable.',
            ]);
        }

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
        $terminalSelection = null;
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
            if ($shippingProvider === 'terminal_africa') {
                $terminalSelection = $this->resolveTerminalSelection(
                    $validated, $this->prepareCheckoutCart($cartItems)['cartItems']
                );
                $shipping = $terminalSelection['total'];
            } else {
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
            }
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
        try {
            $result = DB::transaction(function () use (
                $validated,
                $cartItems,
                $shippingRate,
                $pickupLocation,
                $shipping,
                $shippingProvider,
                $terminalSelection,
                $userId
            ) {
                $prepared = $this->prepareCheckoutCart($cartItems, true);
                if ($shippingProvider === 'terminal_africa') {
                    // Recheck fresh weights, settings, address and expiry under the cart locks.
                    $terminalSelection = $this->resolveTerminalSelection($validated, $prepared['cartItems']);
                    $shipping = $terminalSelection['total'];
                }
                foreach ($prepared['cartItems'] as $fresh) {
                    $previous = $cartItems->get($fresh->id);
                    if (! $previous || abs((float) $previous->price - $fresh->price) > 0.005
                        || $previous->attributes->get('currency') !== 'NGN') {
                        throw ValidationException::withMessages([
                            'cart' => 'Prices or currency have changed. Review the refreshed cart before paying.',
                        ]);
                    }
                }
                $cartItems = $prepared['cartItems'];
                $subtotal = $prepared['subtotal'];
                $vat = 0;
                try {
                    $couponResult = $this->resolveCheckoutCoupon($cartItems, $subtotal);
                } catch (\RuntimeException $e) {
                    session()->forget('coupon');
                    throw ValidationException::withMessages(['coupon' => $e->getMessage()]);
                }
                $coupon = $couponResult['coupon'];
                $discount = $couponResult['discount'];
                $total = round($subtotal + $shipping + $vat - $discount, 2);
                if ($subtotal <= 0 || $total <= 0 || $total > 9999999999999.99) {
                    throw ValidationException::withMessages(['cart' => 'Invalid order amount.']);
                }
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
                            'city' => $terminalSelection['city'] ?? $shippingRate?->zone_name,
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
                    $orderCity = $terminalSelection['city'] ?? $shippingRate?->zone_name;
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
                    'shipping_provider' => $shippingProvider,
                    'shipping_base_cost' => $terminalSelection['base_cost'] ?? $shipping,
                    'shipping_markup_percentage' => $terminalSelection['markup_percentage'] ?? 0,
                    'shipping_markup_amount' => $terminalSelection['markup_amount'] ?? 0,
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
                if ($terminalSelection) {
                    $order->forceFill([
                        'terminal_shipping_snapshot' => json_encode($terminalSelection, JSON_THROW_ON_ERROR),
                    ])->save();
                }
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
                    $isWholesale = $item->attributes->get('purchase_type') === 'wholesale';
                    if ($isWholesale) {
                        $itemName .= ' [Wholesale]';
                    }
                    $snapshotOptions = $variantName ? ['variant' => $variantName] : [];
                    $snapshotOptions['_purchase_type'] = $isWholesale ? 'wholesale' : 'retail';
                    if ($isWholesale) {
                        $snapshotOptions['_wholesale_min_quantity'] = $item->attributes->get('wholesale_min_quantity');
                        $snapshotOptions['_wholesale_qualifying_quantity'] = $item->attributes->get('wholesale_qualifying_quantity');
                        $snapshotOptions['_wholesale_allow_mixed_variants'] = $item->attributes->get('wholesale_allow_mixed_variants');
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
                        'variant_options' => $snapshotOptions,
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
        } catch (ValidationException $e) {
            session(['currency' => 'NGN']);

            return redirect()->route('cart.index')
                ->with('error', collect($e->errors())->flatten()->first());
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
                if ($order->payment_status === 'refunded') {
                    return $this->redirectAfterOrder($order, 'info', 'This payment has already been refunded.');
                }
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
                $paymentMessage = $order->status === 'processing'
                    ? 'Payment successful. Your order is now being processed.'
                    : 'Payment received. Your order requires review before fulfilment.';
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
                            $paymentMessage
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
        if ($cartItems->contains(fn ($item) => $item->attributes->get('purchase_type') === 'wholesale')) {
            session()->forget('coupon');

            return ['coupon' => null, 'discount' => 0.0];
        }
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

    private function prepareCheckoutCart($cartItems, bool $lock = false): array
    {
        $rows = [];
        foreach ($cartItems as $item) {
            $productId = $this->resolveCartItemProductId($item);
            if (! $productId) {
                $this->invalid('A cart item has no valid product identity.');
            }
            $rows[(string) $item->id] = [
                'id' => (string) $item->id,
                'product_id' => $productId,
                'variant_id' => $item->attributes->get('variant_id'),
                'quantity' => (int) $item->quantity,
                'wholesale' => $item->attributes->get('purchase_type') === 'wholesale',
            ];
            if (! is_numeric($item->quantity) || (float) $item->quantity !== (float) (int) $item->quantity) {
                $this->invalid('Cart quantities must be whole numbers.');
            }
        }
        $hasWholesale = collect($rows)->contains(fn ($row) => $row['wholesale']);
        $hasRetail = collect($rows)->contains(
            fn ($row) => ! $row['wholesale']
        );

        if ($hasWholesale && $hasRetail) {
            $this->invalid(
                'Retail and wholesale products cannot be purchased together. '
                .'Please complete or clear your current cart before switching.'
            );
        }
        $currency = 'NGN';
        // Until the coupon module supports exclusions, prevent discounted wholesale carts.
        if ($hasWholesale && (collect(Cart::getConditions())->isNotEmpty()
            || Cart::getContent()->contains(fn ($item) => ! empty($item->conditions)))) {
            $this->invalid('Remove the applied discount before adding or updating wholesale products.');
        }
        $productQuery = Product::with('wholesalePriceTiers')
            ->where('status', 1)
            ->whereIn('id', array_unique(array_column($rows, 'product_id')))
            ->orderBy('id');
        if ($lock) {
            $productQuery->lockForUpdate();
        }
        $products = $productQuery->get()->keyBy('id');
        $variantQuery = ProductVariant::with('wholesalePriceTiers')
            ->whereIn('product_id', $products->keys()->all())->orderBy('id');
        if ($lock) {
            $variantQuery->lockForUpdate();
        }
        $variants = $variantQuery->get()->groupBy('product_id');
        foreach ($products as $product) {
            $product->setRelation('variants', $variants->get($product->id, collect()));
        }
        $sources = [];
        $stockTotals = [];
        $wholesaleTotals = [];

        foreach ($rows as $id => $row) {
            if ($row['quantity'] < 1 || $row['quantity'] > self::MAX_QUANTITY) {
                $this->invalid('Cart quantities must be positive whole numbers within the supported limit.');
            }
            $product = $products->get($row['product_id']);
            if (! $product) {
                $this->invalid('A product in your cart is no longer available. Remove it to continue.');
            }
            $variant = null;
            if ($product->has_variants) {
                $variant = $product->variants->firstWhere('id', $row['variant_id']);
                if (! $variant || ! $variant->is_active) {
                    $this->invalid("Select an available option for {$product->name}.");
                }
            } elseif ($row['variant_id'] !== null) {
                $this->invalid("{$product->name} does not accept a product option.");
            }
            $source = $variant ?? $product;
            $stockKey = $product->id.':'.($variant?->id ?? 'simple');
            $stockTotals[$stockKey] = ($stockTotals[$stockKey] ?? 0) + $row['quantity'];
            $sources[$id] = [$product, $variant, $stockKey];
            if ($row['wholesale']) {
                if (! $product->wholesale_enabled || (int) $product->wholesale_min_quantity < 1
                    || ($variant && ! $variant->wholesale_enabled)
                    || $this->kobo($source->wholesale_price_ngn) <= 0) {
                    $this->invalid("{$product->name} or its selected option is unavailable for wholesale.");
                }
                $wholesaleTotals[$product->id] = ($wholesaleTotals[$product->id] ?? 0) + $row['quantity'];
            }
        }

        $lines = [];
        foreach ($rows as $id => $row) {
            [$product, $variant, $stockKey] = $sources[$id];
            $stock = $this->resolveStock($product, $variant);
            if ($stock['status'] === 'out_of_stock'
                || ($stock['track'] && ($stock['status'] !== 'in_stock'
                    || $stockTotals[$stockKey] > $stock['available']))) {
                $this->invalid("{$product->name}: only {$stock['available']} unit(s) available for this option. Retail and wholesale quantities share this stock.");
            }
            $qualifying = null;
            if ($row['wholesale']) {
                $qualifying = $product->has_variants && $product->wholesale_allow_mixed_variants
                    ? $wholesaleTotals[$product->id] : $row['quantity'];
                if ($qualifying < (int) $product->wholesale_min_quantity) {
                    $this->invalid("{$product->name} requires at least {$product->wholesale_min_quantity} wholesale units"
                        .($product->has_variants && ! $product->wholesale_allow_mixed_variants ? ' per option.' : '.'));
                }
                $price = $this->wholesalePrice($variant ?? $product, $qualifying);
            } else {
                $price = $this->resolvePrice($product, $variant, $currency);
            }
            if ($price <= 0) {
                $this->invalid("{$product->name} has no valid purchase price in {$currency}.");
            }
            $existing = Cart::get($id);
            $attributes = array_merge($existing?->attributes->toArray() ?? [], [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'shipping_weight_kg' => ($variant ?? $product)->shipping_weight_kg,
                'variant_options' => $variant?->options,
                'variant_name' => $variant?->name,
                'sku' => $variant?->sku ?? $product->sku,
                'image' => $variant?->image ?: $product->main_image,
                'slug' => $product->slug,
                'currency' => $currency,
                'purchase_type' => $row['wholesale'] ? 'wholesale' : 'retail',
                'wholesale_min_quantity' => $row['wholesale'] ? (int) $product->wholesale_min_quantity : null,
                'wholesale_qualifying_quantity' => $qualifying,
                'wholesale_allow_mixed_variants' => $row['wholesale'] && (bool) $product->wholesale_allow_mixed_variants,
            ]);
            $lines[$id] = [
                'id' => (string) $id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $row['quantity'],
                'attributes' => $attributes,
            ];
        }

        $preparedItems = collect();
        $subtotal = 0.0;
        foreach ($lines as $id => $line) {
            $preparedItems->put($id, (object) [
                'id' => (string) $id, 'name' => $line['name'],
                'price' => $line['price'], 'quantity' => $line['quantity'],
                'attributes' => collect($line['attributes']),
            ]);
            $subtotal += round($line['price'] * $line['quantity'], 2);
        }

        return ['cartItems' => $preparedItems, 'subtotal' => round($subtotal, 2)];
    }

    private function wholesalePrice($source, int $quantity): float
    {
        $price = $this->kobo($source->wholesale_price_ngn);
        foreach ($source->wholesalePriceTiers->sortBy('min_quantity') as $tier) {
            if ((int) $tier->min_quantity <= $quantity) {
                $tierPrice = $this->kobo($tier->price_ngn);
                if ($tierPrice <= 0 || $tierPrice >= $price) {
                    $this->invalid('Wholesale price tiers require correction before ordering.');
                }
                $price = $tierPrice;
            }
        }

        // Darryldecode Cart stores numeric prices; compare tiers in integer kobo first.
        return $price / 100;
    }

    private function kobo($price): int
    {
        if (! preg_match('/^\d{1,13}(?:\.\d{1,2})?$/', (string) $price)) {
            return 0;
        }
        $parts = explode('.', (string) $price, 2);

        return ((int) $parts[0] * 100) + (int) str_pad($parts[1] ?? '', 2, '0');
    }

    private function invalid(string $message): void
    {
        throw ValidationException::withMessages(['cart' => $message]);
    }

    private function resolveStock($product, $variant = null): array
    {
        $source = $variant ?? $product;

        return [
            'available' => (int) ($variant ? $variant->stock_quantity : $product->quantity),
            'track' => (bool) $source->track_stock,
            'status' => $source->stock_status,
        ];
    }

    private function resolvePrice($product, $variant, string $currency): float
    {
        $regularField = $currency === 'USD' ? 'price_usd' : 'price_ngn';
        $saleField = $currency === 'USD' ? 'sale_price_usd' : 'sale_price_ngn';
        $source = $variant && $variant->{$regularField} !== null ? $variant : $product;
        $regularPrice = (float) $source->{$regularField};
        $salePrice = (float) ($source->{$saleField} ?? 0);
        $now = now();
        $saleActive = (! $product->sale_starts_at || $now->gte($product->sale_starts_at))
            && (! $product->sale_ends_at || $now->lte($product->sale_ends_at));

        return $saleActive && $salePrice > 0 && $salePrice < $regularPrice ? $salePrice : $regularPrice;
    }
}
