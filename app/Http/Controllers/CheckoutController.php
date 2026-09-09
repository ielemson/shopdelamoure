<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingRate;
use App\Models\State;
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

        /*
        |--------------------------------------------------------------------------
        | Nigeria Only For Now
        |--------------------------------------------------------------------------
        */

        $countries = Country::whereRaw('LOWER(name) = ?', ['nigeria'])
            ->get();

        $subtotal = (float) Cart::getSubTotal();

        $shipping = 0;
        $vat = 0;
        $discount = 0;

        $total = $subtotal + $shipping + $vat - $discount;

        /*
        |--------------------------------------------------------------------------
        | Customer Addresses
        |--------------------------------------------------------------------------
        |
        | Only registered customers have saved addresses.
        | Guests simply complete the checkout form.
        |
        */

        $addresses = collect();
        $defaultAddress = null;

        if (auth()->check()) {
            $addresses = auth()->user()
                ->address()
                ->with([
                    'country',
                    'state',
                ])
                ->latest()
                ->get();

            $defaultAddress = auth()->user()
                ->address()
                ->with([
                    'country',
                    'state',
                ])
                ->latest()
                ->first();
        }

        return view(
            'frontend.checkout.index',
            compact(
                'cartItems',
                'countries',
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
        return State::where(
            'country_id',
            $country
        )
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Load Shipping Zones
    |--------------------------------------------------------------------------
    |
    | Called after the customer selects a state.
    |
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
    |
    | The browser submits only the selected shipping-rate ID.
    | The actual amount is always retrieved from the database.
    |
    */

    public function shippingRate(Request $request)
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'shipping_rate_id' => [
                'required',
                'integer',
                'exists:shipping_rates,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Confirm State Belongs To Country
        |--------------------------------------------------------------------------
        */

        $stateExists = State::where(
            'id',
            $validated['state_id']
        )
            ->where(
                'country_id',
                $validated['country_id']
            )
            ->exists();

        if (! $stateExists) {
            return response()->json([
                'status' => false,

                'message' => 'The selected state does not belong to the selected country.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Delivery Zone
        |--------------------------------------------------------------------------
        */

        $shippingRate = $this->resolveShippingRate(
            $validated['country_id'],
            $validated['state_id'],
            $validated['shipping_rate_id']
        );

        if (! $shippingRate) {
            return response()->json([
                'status' => false,

                'message' => 'The selected delivery zone is currently unavailable.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        |
        | shipping_cost is the authoritative NGN base shipping amount.
        |
        */

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
        /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        |
        | Registered customer:
        | user_id = authenticated user's ID
        |
        | Guest customer:
        | user_id = null
        |
        */

        $user = auth()->user();
        $userId = $user?->id;

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'shipping_rate_id' => [
                'required',
                'integer',
                'exists:shipping_rates,id',
            ],

            'street_address' => [
                'required',
                'string',
                'max:255',
            ],

            'delivery_note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'delivery_method' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_method' => [
                'required',
                'in:paystack',
            ],

            'terms' => [
                'required',
                'accepted',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Country / State Relationship
        |--------------------------------------------------------------------------
        */

        $stateExists = State::where(
            'id',
            $validated['state_id']
        )
            ->where(
                'country_id',
                $validated['country_id']
            )
            ->exists();

        if (! $stateExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'state_id' => 'The selected state does not belong to the selected country.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        $cartItems = Cart::getContent();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Your shopping cart is empty.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Authoritative Shipping Rate
        |--------------------------------------------------------------------------
        */

        $shippingRate = $this->resolveShippingRate(
            $validated['country_id'],
            $validated['state_id'],
            $validated['shipping_rate_id']
        );

        if (! $shippingRate) {
            return back()
                ->withInput()
                ->withErrors([
                    'shipping_rate_id' => 'The selected delivery zone is currently unavailable.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */

        $subtotal = (float) Cart::getSubTotal();

        $shipping = (float) $shippingRate->shipping_cost;

        $vat = 0;

        $discount = 0;

        $total =
            $subtotal
            + $shipping
            + $vat
            - $discount;

        if ($subtotal <= 0 || $total <= 0) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid order amount.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Order
        |--------------------------------------------------------------------------
        */

        try {
            $result = DB::transaction(
                function () use (
                    $validated,
                    $cartItems,
                    $shippingRate,
                    $subtotal,
                    $shipping,
                    $vat,
                    $discount,
                    $total,
                    $userId
                ) {
                    /*
                    |--------------------------------------------------------------------------
                    | Saved Customer Address
                    |--------------------------------------------------------------------------
                    |
                    | Only registered customers have addresses stored in the
                    | addresses table.
                    |
                    | Guest checkout does NOT create a saved address record.
                    |
                    */

                    $address = null;

                    if ($userId) {
                        $address = Address::updateOrCreate(
                            [
                                'user_id' => $userId,
                            ],
                            [
                                'first_name' => $validated['first_name'],

                                'last_name' => $validated['last_name'],

                                'email' => $validated['email'],

                                'phone' => $validated['phone'],

                                'country_id' => $validated['country_id'],

                                'state_id' => $validated['state_id'],

                                /*
                                |--------------------------------------------------------------------------
                                | Delivery Zone
                                |--------------------------------------------------------------------------
                                |
                                | The current Address structure still uses city.
                                | For now, the selected shipping zone is retained
                                | here for compatibility.
                                |
                                */

                                'city' => $shippingRate->zone_name,

                                'street_address' => $validated['street_address'],

                                'delivery_note' => $validated['delivery_note'] ?? null,

                                'is_default' => true,
                            ]
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Order
                    |--------------------------------------------------------------------------
                    |
                    | Every order receives a complete customer snapshot.
                    |
                    | This means guest orders remain fully usable even though
                    | user_id and address_id are null.
                    |
                    */

                    $order = Order::create([
                        /*
                        |--------------------------------------------------------------------------
                        | Customer Reference
                        |--------------------------------------------------------------------------
                        */

                        'user_id' => $userId,

                        'address_id' => $address?->id,

                        /*
                        |--------------------------------------------------------------------------
                        | Order Number
                        |--------------------------------------------------------------------------
                        */

                        'order_no' => 'ORD-'.strtoupper(
                            uniqid()
                        ),

                        /*
                        |--------------------------------------------------------------------------
                        | Customer Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'first_name' => $validated['first_name'],

                        'last_name' => $validated['last_name'],

                        'email' => trim($validated['email']),

                        'phone' => $validated['phone'],

                        /*
                        |--------------------------------------------------------------------------
                        | Delivery Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'country_id' => $validated['country_id'],

                        'state_id' => $validated['state_id'],

                        /*
                        |--------------------------------------------------------------------------
                        | Current Compatibility
                        |--------------------------------------------------------------------------
                        |
                        | The orders table currently contains "city".
                        | We temporarily store the delivery-zone name here.
                        |
                        */

                        'city' => $shippingRate->zone_name,

                        'address' => $validated['street_address'],

                        /*
                        |--------------------------------------------------------------------------
                        | Correct orders table field: order_note
                        |--------------------------------------------------------------------------
                        */

                        'order_note' => $validated['delivery_note'] ?? null,

                        /*
                        |--------------------------------------------------------------------------
                        | Delivery
                        |--------------------------------------------------------------------------
                        */

                        'delivery_method' => $validated['delivery_method'],

                        /*
                        |--------------------------------------------------------------------------
                        | Currency
                        |--------------------------------------------------------------------------
                        */

                        'currency' => 'NGN',

                        /*
                        |--------------------------------------------------------------------------
                        | Payment
                        |--------------------------------------------------------------------------
                        */

                        'payment_method' => 'paystack',

                        'payment_status' => 'unpaid',

                        /*
                        |--------------------------------------------------------------------------
                        | Amounts
                        |--------------------------------------------------------------------------
                        */

                        'subtotal' => $subtotal,

                        'shipping' => $shipping,

                        'vat' => $vat,

                        'discount' => $discount,

                        'total' => $total,

                        /*
                        |--------------------------------------------------------------------------
                        | Status
                        |--------------------------------------------------------------------------
                        */

                        'status' => 'pending',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Order Items
                    |--------------------------------------------------------------------------
                    */

                    foreach ($cartItems as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,

                            'product_id' => $item->id,

                            'name' => $item->name,

                            'price' => (float) $item->price,

                            'quantity' => $item->quantity,

                            'total' => (float) $item->price
                                * $item->quantity,

                            'image' => $item->attributes->image
                                    ?? null,
                        ]);
                    }

                    return [
                        'order' => $order,

                        'address' => $address,

                        'shippingRate' => $shippingRate,
                    ];
                }
            );

            $order =
                $result['order'];

            $address =
                $result['address'];

            /*
            |--------------------------------------------------------------------------
            | Paystack Configuration
            |--------------------------------------------------------------------------
            */

            $secretKey =
                config(
                    'services.paystack.secret_key'
                );

            $paymentUrl =
                config(
                    'services.paystack.payment_url'
                );

            if (
                ! $secretKey ||
                ! $paymentUrl
            ) {
                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Order saved, but the payment gateway is not configured.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Payment Reference
            |--------------------------------------------------------------------------
            */

            $reference =
                'PSK-'
                .$order->order_no
                .'-'
                .time();

            $order->update([
                'payment_reference' => $reference,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Paystack Email
            |--------------------------------------------------------------------------
            */

            $paystackEmail =
                trim(
                    $validated['email']
                );

            if (
                ! filter_var(
                    $paystackEmail,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
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
                $response = Http::withToken(
                    $secretKey
                )
                    ->acceptJson()
                    ->timeout(30)
                    ->connectTimeout(15)
                    ->post(
                        rtrim(
                            $paymentUrl,
                            '/'
                        )
                            .'/transaction/initialize',
                        [
                            'email' => $paystackEmail,

                            /*
                            |--------------------------------------------------------------------------
                            | Amount In Kobo
                            |--------------------------------------------------------------------------
                            */

                            'amount' => (int) round(
                                $order->total * 100
                            ),

                            'currency' => 'NGN',

                            'reference' => $reference,

                            'callback_url' => route(
                                'checkout.paystack.callback'
                            ),

                            'metadata' => [
                            'order_id' => $order->id,

                            'order_no' => $order->order_no,

                            /*
                            |--------------------------------------------------------------------------
                            | Guest Compatible Metadata
                            |--------------------------------------------------------------------------
                            */

                            'address_id' => $address?->id,

                            'user_id' => $userId,

                            'customer_type' => $userId
                                    ? 'registered'
                                    : 'guest',

                            'shipping_rate_id' => $shippingRate->id,

                            'shipping_zone' => $shippingRate->zone_name,

                            'shipping' => $shipping,
                            ],
                        ]
                    );

                if (
                    ! $response->successful()
                    ||
                    ! (
                        $response->json(
                            'status'
                        ) ?? false
                    )
                ) {
                    Log::error(
                        'Paystack initialization failed',
                        [
                            'order_id' => $order->id,

                            'response' => $response->json(),
                        ]
                    );

                    return $this->redirectAfterOrder(
                        $order,
                        'error',
                        'Order saved, but payment could not be initialized. Please try again.'
                    );
                }

                $authorizationUrl =
                    $response->json(
                        'data.authorization_url'
                    );

                if (! $authorizationUrl) {
                    Log::error(
                        'Paystack authorization URL missing',
                        [
                            'order_id' => $order->id,

                            'response' => $response->json(),
                        ]
                    );

                    return $this->redirectAfterOrder(
                        $order,
                        'error',
                        'Order saved, but the payment page could not be opened.'
                    );
                }

                return redirect()->away(
                    $authorizationUrl
                );
            } catch (
                \Illuminate\Http\Client\ConnectionException $e
            ) {
                Log::error(
                    'Paystack connection error',
                    [
                        'order_id' => $order->id,

                        'message' => $e->getMessage(),
                    ]
                );

                return $this->redirectAfterOrder(
                    $order,
                    'error',
                    'Order saved, but the payment gateway is currently unreachable. Please try again later.'
                );
            }
        } catch (\Throwable $e) {
            Log::error(
                'Checkout store error',
                [
                    'message' => $e->getMessage(),

                    'file' => $e->getFile(),

                    'line' => $e->getLine(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Something went wrong during checkout. Please try again.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout Process Compatibility
    |--------------------------------------------------------------------------
    |
    | checkout.process continues using the same checkout logic.
    |
    */

    public function process(Request $request)
    {
        return $this->store(
            $request
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Paystack Callback
    |--------------------------------------------------------------------------
    */

    public function paystackCallback(
        Request $request
    ) {
        $reference =
            $request->query(
                'reference'
            );

        if (! $reference) {
            return redirect()
                ->route(
                    'checkout.index'
                )
                ->with(
                    'error',
                    'Payment reference missing.'
                );
        }

        $secretKey =
            config(
                'services.paystack.secret_key'
            );

        $paymentUrl =
            config(
                'services.paystack.payment_url'
            );

        if (
            ! $secretKey ||
            ! $paymentUrl
        ) {
            return redirect()
                ->route(
                    'checkout.index'
                )
                ->with(
                    'error',
                    'Payment gateway is not configured.'
                );
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Verify Transaction
            |--------------------------------------------------------------------------
            */

            $response = Http::withToken(
                $secretKey
            )
                ->acceptJson()
                ->timeout(30)
                ->connectTimeout(15)
                ->get(
                    rtrim(
                        $paymentUrl,
                        '/'
                    )
                        .'/transaction/verify/'
                        .urlencode(
                            $reference
                        )
                );

            if (
                ! $response->successful()
                ||
                ! (
                    $response->json(
                        'status'
                    ) ?? false
                )
            ) {
                return redirect()
                    ->route(
                        'checkout.index'
                    )
                    ->with(
                        'error',
                        'Could not verify payment.'
                    );
            }

            $data =
                $response->json(
                    'data'
                );

            if (! is_array($data)) {
                return redirect()
                    ->route(
                        'checkout.index'
                    )
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
                    ->route(
                        'checkout.index'
                    )
                    ->with(
                        'error',
                        'Order could not be found for this payment.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Confirm Reference
            |--------------------------------------------------------------------------
            */

            if (
                ($data['reference'] ?? null)
                !==
                $order->payment_reference
            ) {
                Log::warning(
                    'Paystack reference mismatch',
                    [
                        'order_id' => $order->id,

                        'expected' => $order->payment_reference,

                        'received' => $data['reference']
                                ?? null,
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
            | Confirm Currency
            |--------------------------------------------------------------------------
            */

            if (
                strtoupper(
                    $data['currency']
                        ?? ''
                )
                !==
                'NGN'
            ) {
                Log::warning(
                    'Paystack currency mismatch',
                    [
                        'order_id' => $order->id,

                        'currency' => $data['currency']
                                ?? null,
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
            | Confirm Amount
            |--------------------------------------------------------------------------
            */

            $expectedAmount =
                (int) round(
                    $order->total * 100
                );

            $paidAmount =
                (int) (
                    $data['amount']
                    ?? 0
                );

            if (
                $paidAmount
                !==
                $expectedAmount
            ) {
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

            if (
                ($data['status'] ?? '')
                ===
                'success'
            ) {
                if (
                    $order->payment_status
                    !==
                    'paid'
                ) {
                    $order->update([
                        'payment_status' => 'paid',

                        'status' => 'processing',

                        'paid_at' => now(),

                        'payment_gateway_response' => json_encode(
                            $data
                        ),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Clear Cart
                |--------------------------------------------------------------------------
                */

                Cart::clear();

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
                            'Payment successful.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Guest Customer
                |--------------------------------------------------------------------------
                |
                | A dedicated guest order confirmation/tracking page will be
                | introduced in the next appropriate step.
                |
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
            */

            $order->update([
                'payment_status' => 'failed',

                'payment_gateway_response' => json_encode(
                    $data
                ),
            ]);

            return $this->redirectAfterOrder(
                $order,
                'error',
                'Payment was not successful.'
            );
        } catch (\Throwable $e) {
            Log::error(
                'Paystack verification error',
                [
                    'reference' => $reference,

                    'message' => $e->getMessage(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | We may not have resolved the order at this point.
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
                ->route(
                    'checkout.index'
                )
                ->with(
                    'error',
                    'Unable to verify payment at this time.'
                );
        }
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
        return ShippingRate::with(
            'areas'
        )
            ->where(
                'id',
                $shippingRateId
            )
            ->where(
                'country_id',
                $countryId
            )
            ->where(
                'state_id',
                $stateId
            )
            ->where(
                'is_active',
                true
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect After Order Creation
    |--------------------------------------------------------------------------
    |
    | Registered customers can use their protected customer order page.
    |
    | Guests do not yet have access to that page, so they are returned to
    | the shop with their order number.
    |
    */

    private function redirectAfterOrder(
        Order $order,
        string $type,
        string $message
    ) {
        if ($order->user_id) {
            return redirect()
                ->route(
                    'customer.orders.show',
                    $order->id
                )
                ->with(
                    $type,
                    $message
                );
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
