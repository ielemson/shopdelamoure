<?php
// Required once at the bottom of routes/web.php. Do not replace your existing routes.
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DeliverySettingController;
use App\Http\Controllers\Admin\TerminalDeliveryController;
use App\Http\Controllers\TerminalDeliveryWebhookController;

Route::middleware(['auth', 'permission:manage store'])->group(function () {
    Route::get('/admin/terminal-delivery', [TerminalDeliveryController::class, 'index'])->name('admin.terminal-delivery.index');
    Route::post('/admin/terminal-delivery/preview', [DeliverySettingController::class, 'preview']);
    Route::post('/admin/terminal-delivery/preview/end', [DeliverySettingController::class, 'endPreview']);
    Route::post('/admin/terminal-delivery/{order}/book', [TerminalDeliveryController::class, 'book'])->name('admin.terminal-delivery.book');
    Route::post('/admin/terminal-delivery/{order}/refresh', [TerminalDeliveryController::class, 'refresh'])->name('admin.terminal-delivery.refresh');
});
Route::get('/orders/{order}/delivery', [TerminalDeliveryController::class, 'tracking'])->middleware('auth')->name('orders.delivery');
// Exempt ONLY this URL from CSRF; the controller verifies the provider signature.
Route::post('/webhooks/terminal-delivery', TerminalDeliveryWebhookController::class);
// Keep existing routes under these names; add only if absent.
if (!Route::has('checkout.terminal.cities')) Route::get('/checkout/terminal/cities', [\App\Http\Controllers\CheckoutController::class, 'terminalCities'])->name('checkout.terminal.cities');
if (!Route::has('checkout.terminal.rates')) Route::post('/checkout/terminal/rates', [\App\Http\Controllers\CheckoutController::class, 'terminalRates'])->middleware('throttle:10,1')->name('checkout.terminal.rates');
