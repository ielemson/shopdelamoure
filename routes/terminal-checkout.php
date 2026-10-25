<?php
// Require at the top level of routes/web.php, OUTSIDE the admin group.
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
Route::get('/checkout/terminal/cities', [CheckoutController::class, 'terminalCities'])->middleware('throttle:30,1')->name('checkout.terminal.cities');
Route::post('/checkout/terminal/rates', [CheckoutController::class, 'terminalRates'])->middleware('throttle:3,1')->name('checkout.terminal.rates');
