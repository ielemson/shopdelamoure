<?php
namespace App\Services;

use App\Mail\AdminNewOrderMail;
use App\Mail\CustomerOrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class OrderPaymentService
{
    public function __construct(protected InventoryService $inventoryService) {}

    /** Caller must obtain gatewayData from Paystack verification or a verified webhook. */
    public function processSuccessfulPayment(Order $order, array $gatewayData): bool
    {
        $result = DB::transaction(function () use ($order, $gatewayData) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (($gatewayData['status'] ?? null) !== 'success'
                || ($gatewayData['reference'] ?? null) !== $lockedOrder->payment_reference
                || strtoupper((string) ($gatewayData['currency'] ?? '')) !== strtoupper((string) $lockedOrder->currency)
                || !is_numeric($gatewayData['amount'] ?? null)
                || (float) $gatewayData['amount'] !== (float) (int) round((float) $lockedOrder->total * 100)) {
                throw new RuntimeException('The payment does not match this order.');
            }
            if ($lockedOrder->payment_status === 'refunded') {
                return ['just_paid' => false, 'inventory_error' => null, 'recovered' => false];
            }
            $justPaid = $lockedOrder->payment_status !== 'paid';
            if ($justPaid) $lockedOrder->update(['payment_status' => 'paid', 'paid_at' => now(),
                'payment_gateway_response' => json_encode($gatewayData, JSON_THROW_ON_ERROR)]);
            $this->recordCouponUsage($lockedOrder);
            $inventoryError = null;
            $wasPending = $lockedOrder->status === 'pending';
            try {
                if (in_array($lockedOrder->status, ['cancelled', 'canceled'], true)) {
                    throw new RuntimeException('A cancelled order received payment; manual review is required.');
                }
                $this->inventoryService->deductOrderStock($lockedOrder);
            } catch (Throwable $e) {
                if ($e instanceof \Illuminate\Database\QueryException
                    && (in_array((string) $e->getCode(), ['40001', '40P01'], true)
                        || in_array((int) ($e->errorInfo[1] ?? 0), [1205, 1213], true))) throw $e;
                $inventoryError = $e->getMessage();
            }
            if ($inventoryError === null && ($justPaid || $wasPending)) $lockedOrder->update(['status' => 'processing']);
            return ['just_paid' => $justPaid, 'inventory_error' => $inventoryError,
                'recovered' => !$justPaid && $wasPending && $inventoryError === null];
        }, 5);

        DB::afterCommit(function () use ($order, $result) {
            $order->refresh();
            $order->loadMissing(['items', 'items.product', 'items.variant', 'country', 'state', 'pickupLocation.state']);
            if ($result['inventory_error'] !== null) Log::critical('Paid order requires inventory review', [
                'order_id' => $order->id, 'order_no' => $order->order_no, 'message' => $result['inventory_error']]);
            // Register durable work after payment/stock commit. Scheduler performs external booking.
            if ($result['inventory_error'] === null && $order->payment_status === 'paid' && $order->status === 'processing') {
                try { app(TerminalDeliveryService::class)->register($order); }
                catch (Throwable $e) { Log::error('Terminal delivery registration failed', ['order_id' => $order->id]); }
            }
            if (!$result['just_paid'] && !$result['recovered']) return;
            if ($result['inventory_error'] === null) {
                try {
                    if ($order->email && filter_var($order->email, FILTER_VALIDATE_EMAIL)) Mail::to($order->email)->send(new CustomerOrderConfirmationMail($order));
                } catch (Throwable $e) {
                    Log::error('Customer order confirmation email failed', ['order_id' => $order->id,
                        'order_no' => $order->order_no, 'email' => $order->email, 'message' => $e->getMessage()]);
                }
            }
            if (!$result['just_paid']) return;
            try {
                $adminEmail = config('mail.admin_address');
                if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) Mail::to($adminEmail)->send(new AdminNewOrderMail($order));
            } catch (Throwable $e) {
                Log::error('Admin new order email failed', ['order_id' => $order->id,
                    'order_no' => $order->order_no, 'admin_email' => $adminEmail ?? null, 'message' => $e->getMessage()]);
            }
        });
        return $result['just_paid'];
    }

    private function recordCouponUsage(Order $order): void
    {
        if (!$order->coupon_id || $order->payment_status !== 'paid') return;
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (!$lockedOrder->coupon_id || $lockedOrder->payment_status !== 'paid' || $lockedOrder->coupon_usage_counted_at) return;
            $coupon = \App\Models\Coupon::query()->lockForUpdate()->find($lockedOrder->coupon_id);
            if ($coupon) $coupon->increment('usage_count');
            $lockedOrder->forceFill(['coupon_usage_counted_at' => now()])->save();
        });
    }
}
