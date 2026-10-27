<?php
namespace App\Http\Controllers;
use App\Models\WebsiteSetting;
use App\Services\TerminalDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerminalDeliveryWebhookController extends Controller
{
    public function __invoke(Request $request, TerminalDeliveryService $service)
    {
        if (strlen($request->getContent()) > 1048576) abort(413);
        $setting = WebsiteSetting::firstOrCreate([]);
        $secret = trim((string) (($setting->terminal_credentials ?? [])['live'] ?? ''));
        $signature = strtolower((string) $request->header('X-Terminal-Signature', ''));
        abort_unless($secret !== '' && preg_match('/^[a-f0-9]{128}$/', $signature)
            && hash_equals(hash_hmac('sha512', $request->getContent(), $secret), $signature), 401);
        $payload = $request->json()->all();
        if (!in_array($payload['event'] ?? '', ['shipment.created', 'shipment.updated'], true)) return response()->json(['received' => true]);
        $shipmentId = data_get($payload, 'data.shipment_id');
        if (!is_string($shipmentId) || strlen($shipmentId) > 255) return response()->json(['received' => true]);
        $row = DB::table('terminal_deliveries')->where('shipment_id', $shipmentId)->first();
        if ($row) {
            // Fetch authoritative current state. Replayed/out-of-order events cannot regress status.
            try { $service->sync($row->order_id); }
            catch (\Throwable $e) { return response()->json(['received' => false], 503); }
        }
        return response()->json(['received' => true]);
    }
}
