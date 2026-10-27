<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TerminalDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerminalDeliveryController extends Controller
{
    public function index()
    {
        $deliveries = DB::table('terminal_deliveries')->join('orders', 'orders.id', '=', 'terminal_deliveries.order_id')
            ->select('terminal_deliveries.*', 'orders.order_no', 'orders.payment_status')
            ->orderByDesc('terminal_deliveries.id')->paginate(25);
        return view('delivery.operations', compact('deliveries'));
    }
    public function book(Order $order, TerminalDeliveryService $service)
    {
        $service->register($order);
        $service->book($order->id, true);
        return back()->with('success', 'Booking processed. Review the resulting status below.');
    }
    public function refresh(Order $order, TerminalDeliveryService $service)
    {
        try { $service->sync($order->id); }
        catch (\Throwable $e) { return back()->withErrors(['delivery' => 'Unable to refresh tracking. Check connectivity and Terminal account access.']); }
        return back()->with('success', 'Tracking refreshed.');
    }
    public function tracking(Order $order)
    {
        abort_unless((auth()->id() && (string) $order->user_id === (string) auth()->id())
            || auth()->user()?->can('manage store'), 403);
        $delivery = DB::table('terminal_deliveries')->where('order_id', $order->id)->first();
        return view('delivery.tracking', compact('order', 'delivery'));
    }
}
