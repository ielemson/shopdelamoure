<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\State;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{

    public function index()
    {
        $cartItems = Cart::getContent();

        $countries = Country::where("id", 161)->get();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('shop')
                ->with('error', 'Your cart is empty.');
        }

        $subtotal = Cart::getSubTotal();

        $vat = 0;

        $shipping = 0;

        $discount = 0;

        $total = $subtotal + $shipping + $vat - $discount;

        $addresses = auth()->user()
            ->address()
            ->with(['country', 'state'])
            ->latest()
            ->get();

        $defaultAddress = auth()->user()
            ->address()
            ->first();

        return view('frontend.checkout.index', compact(
            'cartItems',
            'countries',
            'subtotal',
            'shipping',
            'vat',
            'discount',
            'total',
            'addresses',
            'defaultAddress'
        ));
    }

    public function store(Request $request)
    {
        try {
            $cartItems = Cart::getContent();

            if ($cartItems->isEmpty()) {
                return back()->with('error', 'Your cart is empty.');
            }

            $validator = Validator::make($request->all(), [
                'first_name'      => 'required|string|max:100',
                'last_name'       => 'required|string|max:100',
                'email'           => 'required|email|max:150',
                'phone'           => 'required|string|max:30',
                'country_id'      => 'required|exists:countries,id',
                'state_id'        => 'required|exists:states,id',
                'city'            => 'required|string|max:100',
                'street_address'  => 'required|string|max:255',
                'delivery_note'   => 'nullable|string',
                'delivery_method' => 'required|string',
                'payment_method'  => 'required|string',
                'terms'           => 'required|accepted',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please complete all required checkout fields.');
            }

            $subtotal = Cart::getSubTotal();
            $shipping = 0;
            $vat = 0;
            $discount = 0;
            $total = $subtotal + $shipping + $vat - $discount;

            $result = DB::transaction(function () use (
                $request,
                $cartItems,
                $subtotal,
                $shipping,
                $vat,
                $discount,
                $total
            ) {
                $address = Address::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                    ],
                    [
                        'first_name'     => $request->first_name,
                        'last_name'      => $request->last_name,
                        'email'          => $request->email,
                        'phone'          => $request->phone,
                        'country_id'     => $request->country_id,
                        'state_id'       => $request->state_id,
                        'city'           => $request->city,
                        'delivery_note'  => $request->delivery_note,
                        'street_address' => $request->street_address,
                        'is_default'     => true,
                    ]
                );

                $order = Order::create([
                    'user_id'         => auth()->id(),
                    'address_id'      => $address->id,
                    'order_no'        => 'ORD-' . strtoupper(uniqid()),
                    'delivery_note'      => $request->delivery_notes,
                    'delivery_method' => $request->delivery_method,
                    'payment_method'  => $request->payment_method,
                    'subtotal'        => $subtotal,
                    'shipping'        => $shipping,
                    'vat'             => $vat,
                    'discount'        => $discount,
                    'total'           => $total,
                    'status'          => 'pending',
                    'payment_status'  => 'unpaid',
                ]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->id,
                        'name'       => $item->name,
                        'price'      => $item->price,
                        'quantity'   => $item->quantity,
                        'total'      => $item->price * $item->quantity,
                        'image'      => $item->attributes->image ?? null,
                    ]);
                }

                return [
                    'order'   => $order,
                    'address' => $address,
                ];
            });

            $order = $result['order'];
            $address = $result['address'];

            if ($request->payment_method !== 'paystack') {
                Cart::clear();

                return redirect()
                    ->route('customer.orders.show', $order->id)
                    ->with('success', 'Your order has been placed successfully.');
            }

            if (!config('services.paystack.secret_key')) {
                return redirect()
                    ->route('customer.orders.show', $order->id)
                    ->with('error', 'Order saved, but payment gateway is not configured.');
            }

            $reference = 'PSK-' . $order->order_no . '-' . time();

            $order->update([
                'payment_reference' => $reference,
            ]);

            $paystackEmail = trim($request->email);

            if (!filter_var($paystackEmail, FILTER_VALIDATE_EMAIL)) {
                return redirect()
                    ->route('customer.orders.show', $order->id)
                    ->with('error', 'Order saved, but the email address is invalid for payment.');
            }

            try {
                $response = Http::withToken(config('services.paystack.secret_key'))
                    ->timeout(30)
                    ->connectTimeout(15)
                    ->post(config('services.paystack.payment_url') . '/transaction/initialize', [
                        'email'        => $paystackEmail,
                        'amount'       => (int) round($order->total * 100),
                        'currency'     => 'NGN',
                        'reference'    => $reference,
                        'callback_url' => route('checkout.paystack.callback'),
                        'metadata'     => [
                            'order_id'   => $order->id,
                            'order_no'   => $order->order_no,
                            'address_id' => $address->id,
                            'user_id'    => auth()->id(),
                        ],
                    ]);

                if (!$response->successful() || !($response['status'] ?? false)) {
                    Log::error('Paystack initialization failed', [
                        'order_id' => $order->id,
                        'response' => $response->json(),
                    ]);

                    return redirect()
                        ->route('customer.orders.show', $order->id)
                        ->with('error', 'Order saved, but payment could not be initialized. Please try payment again.');
                }

                return redirect()->away($response['data']['authorization_url']);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Paystack connection error', [
                    'order_id' => $order->id,
                    'message'  => $e->getMessage(),
                ]);

                return redirect()
                    ->route('customer.orders.show', $order->id)
                    ->with('error', 'Order saved, but payment gateway is currently unreachable. Please try again later.');
            }
        } catch (\Throwable $e) {
            Log::error('Checkout store error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong during checkout. Please try again.');
        }
    }
    public function paystackCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('checkout.index')->with('error', 'Payment reference missing.');
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get(config('services.paystack.payment_url') . '/transaction/verify/' . $reference);

        if (!$response->successful() || !($response['status'] ?? false)) {
            return redirect()->route('checkout.index')->with('error', 'Could not verify payment.');
        }

        $data = $response['data'];

        $order = Order::where('payment_reference', $reference)->firstOrFail();

        if (($data['status'] ?? '') === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
                'payment_gateway_response' => json_encode($data),
            ]);

            Cart::clear();

            return redirect()
                ->route('customer.orders.show', $order->id)
                ->with('success', 'Payment successful.');
        }

        $order->update([
            'payment_status' => 'failed',
            'payment_gateway_response' => json_encode($data),
        ]);

        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment was not successful.');
    }

    public function states($country)
    {
        return State::where('country_id', $country)
            ->orderBy('name')
            ->get(['id', 'name']);
    }
    public function cities($stateId)
    {
        return City::where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Process the checkout form submission

    public function process(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login or create an account to continue checkout.');
        }

        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:150',
            'phone'          => 'required|string|max:30',
            'address'        => 'required|string|max:255',
            'city'           => 'required',
            'state'          => 'required',
            'country'        => 'required',
            'order_note'     => 'nullable|string|max:1000',
            'payment_method' => 'required|in:whatsapp,paystack',
        ]);

        $cart = Cart::getContent();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your shopping cart is empty.');
        }

        if ($validated['payment_method'] === 'whatsapp') {
            return $this->processWhatsAppOrder($validated, $cart);
        }

        return $this->initializePaystack($validated, $cart);
    }

    protected function processWhatsAppOrder(array $data, $cart)
    {
        $currency = strtoupper(session('currency', 'NGN'));
        $symbol = $currency === 'USD' ? '$' : '₦';

        $subtotal = (float) Cart::getSubTotal();

        $message = "Hello Delamoure,\n\n";
        $message .= "I would like to place an order.\n\n";

        $message .= "*CUSTOMER DETAILS*\n";
        $message .= "Name: {$data['first_name']} {$data['last_name']}\n";
        $message .= "Email: {$data['email']}\n";
        $message .= "Phone: {$data['phone']}\n";
        $message .= "Address: {$data['address']}\n";
        $message .= "City: {$data['city']}\n";
        $message .= "State: {$data['state']}\n";
        $message .= "Country: {$data['country']}\n\n";

        $message .= "*ORDER DETAILS*\n";

        foreach ($cart as $item) {
            $lineTotal = $item->price * $item->quantity;

            $message .= "• {$item->name}";
            $message .= " x{$item->quantity}";
            $message .= " - {$symbol}" . number_format($lineTotal, 2);

            if ($item->attributes->get('variant_name')) {
                $message .= " ({$item->attributes->get('variant_name')})";
            }

            $message .= "\n";
        }

        $message .= "\n*Subtotal:* {$symbol}" . number_format($subtotal, 2);

        if (!empty($data['order_note'])) {
            $message .= "\n\n*Order Note:*\n{$data['order_note']}";
        }

        $number = config('services.whatsapp.number');

        if (!$number) {
            return back()
                ->withInput()
                ->with('error', 'WhatsApp ordering is currently unavailable.');
        }

        $url = 'https://wa.me/' . $number . '?text=' . rawurlencode($message);

        return redirect()->away($url);
    }

    protected function initializePaystack(array $data, $cart)
    {
        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return back()
                ->withInput()
                ->with('error', 'Paystack payment is currently unavailable.');
        }

        $currency = strtoupper(session('currency', 'NGN'));
        $total = (float) Cart::getSubTotal();

        if ($total <= 0) {
            return back()
                ->withInput()
                ->with('error', 'Invalid order amount.');
        }

        $reference = 'DLM-' . now()->format('YmdHis') . '-' .
            strtoupper(Str::random(8));

        try {

            $response = Http::withToken($secretKey)
                ->acceptJson()
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email'        => $data['email'],
                    'amount'       => (int) round($total * 100),
                    'currency'     => $currency,
                    'reference'    => $reference,
                    'callback_url' => route('checkout.paystack.callback'),
                    'metadata'     => [
                        'user_id'    => auth()->id(),
                        'first_name' => $data['first_name'],
                        'last_name'  => $data['last_name'],
                        'phone'      => $data['phone'],
                    ],
                ]);

            if (!$response->successful() || !$response->json('status')) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $response->json('message')
                            ?? 'Unable to initialize Paystack payment.'
                    );
            }

            session([
                'checkout_paystack_reference' => $reference,
                'checkout_data' => $data,
            ]);

            return redirect()->away(
                $response->json('data.authorization_url')
            );
        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to connect to Paystack. Please try again.');
        }
    }
}
