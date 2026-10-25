<?php
// Require INSIDE your existing authenticated, authorized admin route group.
use App\Http\Controllers\Admin\TerminalCheckoutSettingController;
use Illuminate\Support\Facades\Route;
Route::get('/terminal-checkout', [TerminalCheckoutSettingController::class, 'edit'])->name('terminal-checkout.edit');
Route::put('/terminal-checkout', [TerminalCheckoutSettingController::class, 'update'])->name('terminal-checkout.update');
Route::post('/terminal-checkout/preview', [TerminalCheckoutSettingController::class, 'preview'])->name('terminal-checkout.preview');
Route::post('/terminal-checkout/stop', [TerminalCheckoutSettingController::class, 'stop'])->name('terminal-checkout.stop');
