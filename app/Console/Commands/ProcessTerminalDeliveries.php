<?php
namespace App\Console\Commands;
use App\Models\Order;
use App\Services\TerminalDeliveryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessTerminalDeliveries extends Command
{
    protected $signature = 'terminal:deliveries {--limit=20}';
    protected $description = 'Register paid orders, book eligible deliveries, and refresh tracking';
    public function handle(TerminalDeliveryService $service): int
    {
        $limit = max(1, min(100, (int) $this->option('limit')));
        Order::query()->where('payment_status', 'paid')->where('status', 'processing')
            ->where('delivery_method', 'shipping')->whereNotNull('terminal_shipping_snapshot')
            ->whereNotExists(function ($q) { $q->select(DB::raw(1))->from('terminal_deliveries')->whereColumn('terminal_deliveries.order_id', 'orders.id'); })
            ->orderBy('id')->limit($limit)->get()->each(fn($order) => $service->register($order));
        // Stopped workers need review, not a blind retry of a possibly charged shipment.
        DB::table('terminal_deliveries')->where('state', 'booking')->where('attempted_at', '<', now()->subMinutes(15))
            ->update(['state' => 'review', 'last_error' => 'Worker stopped during booking. Check Terminal before further action.', 'updated_at' => now()]);
        foreach (DB::table('terminal_deliveries')->where('state', 'ready')->orderBy('id')->limit($limit)->get() as $row) {
            try { $service->book($row->order_id); } catch (\Throwable $e) { $this->warn('Booking needs review for order '.$row->order_id); }
        }
        foreach (DB::table('terminal_deliveries')->whereIn('state', ['booked', 'review'])->whereNotNull('shipment_id')
            ->orderBy('synced_at')->limit($limit)->get() as $row) {
            try { $service->sync($row->order_id); } catch (\Throwable $e) { $this->warn('Tracking refresh failed for order '.$row->order_id); }
        }
        return self::SUCCESS;
    }
}
