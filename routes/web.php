<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;

use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\CategoryProductController;
use App\Http\Controllers\Frontend\CartController as FrontendCartController;

// use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\WebsiteSettingController;

// use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Frontend\ContactController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'index'])->name('index');
Route::get('/about-us', [FrontendController::class, 'about'])->name('about');
Route::get('/contact-us', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::get('/collections/shop', [FrontendProductController::class, 'shop'])->name('shop');
Route::get('/product/search', [FrontendProductController::class, 'search'])->name('products.search');
Route::get('/product/{slug}', [FrontendProductController::class, 'show'])->name('products.show');
Route::get('/product/quick-view/{product:slug}', [FrontendProductController::class, 'quickView'])->name('products.quick-view');
Route::get('/category/{slug}', [CategoryProductController::class, 'show'])->name('category.products');

Route::post('/shopping/cart', [FrontendCartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [FrontendCartController::class, 'add'])->name('cart.add');
Route::get('/cart', [FrontendCartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [FrontendCartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [FrontendCartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [FrontendCartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/sidebar', [FrontendCartController::class, 'sidebar'])->name('cart.sidebar');
Route::delete('/cart/{id}', [FrontendCartController::class, 'removeCartItem'])->name('cart.removeCartItem');

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::get('/checkout/states/{country}', [CheckoutController::class, 'states'])
    ->name('checkout.states');

Route::get('/checkout/cities/{stateId}', [CheckoutController::class, 'cities'])
    ->name('checkout.cities');

Route::post('/checkout/auth-check', function () {
    return response()->json([
        'status' => auth()->check(),
        'message' => 'Please login or create an account to continue checkout.',
        'redirect' => route('login'),
    ]);
})->name('checkout.auth.check');

Route::post('/checkout/process', [CheckoutController::class, 'process'])
    ->name('checkout.process');

Route::get('/checkout/paystack/callback', [CheckoutController::class, 'paystackCallback'])
    ->name('checkout.paystack.callback');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:Admin'])
    ->group(function () {

        // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', AdminCategoryController::class);
        Route::resource('products', AdminProductController::class);

        Route::get('/website-settings', [WebsiteSettingController::class, 'edit'])
            ->name('website.settings');

        Route::put('/website-settings', [WebsiteSettingController::class, 'update'])
            ->name('website.settings.update');
        Route::resource('customers', CustomerController::class);

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');

        Route::patch('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])
            ->name('orders.updatePaymentStatus');
    });


Route::prefix('customer')
    ->name('customer.')
    ->middleware(['auth', 'role:Customer'])
    ->group(function () {

        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/my-orders', [CustomerOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])
            ->name('orders.show');

        Route::get('/addresses', [CustomerAddressController::class, 'index'])
            ->name('addresses.index');

        Route::get('/addresses/create', [CustomerAddressController::class, 'create'])
            ->name('addresses.create');

        Route::post('/addresses', [CustomerAddressController::class, 'store'])
            ->name('addresses.store');

        Route::get('/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])
            ->name('addresses.edit');

        Route::put('/addresses/{address}', [CustomerAddressController::class, 'update'])
            ->name('addresses.update');

        Route::delete('/addresses/{address}', [CustomerAddressController::class, 'destroy'])
            ->name('addresses.destroy');
    });



Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::get('/checkout/states/{countryId}', [CheckoutController::class, 'states'])
        ->name('checkout.states');

    Route::get('/checkout/cities/{stateId}', [CheckoutController::class, 'cities'])
        ->name('checkout.cities');
});
