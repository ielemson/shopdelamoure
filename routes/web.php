<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController;
/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

// General
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ShippingRateController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CustomerAddressController;
// Frontend
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Frontend\CartController as FrontendCartController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CurrencyController;
// Admin
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// // Customer
// use App\Http\Controllers\Customer\CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| Frontend Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'index'])
    ->name('index');

Route::get('/about-us', [FrontendController::class, 'about'])
    ->name('about');

Route::get('/contact-us', [FrontendController::class, 'contact'])
    ->name('contact');

Route::post('/contact/send', [ContactController::class, 'send'])
    ->name('contact.send');

/*
|--------------------------------------------------------------------------
| Products / Shop
|--------------------------------------------------------------------------
*/

Route::get('/collections/shop', [FrontendProductController::class, 'shop'])
    ->name('shop');

Route::get('/product/search', [FrontendProductController::class, 'search'])
    ->name('products.search');

Route::get('/product/quick-view/{product:slug}', [FrontendProductController::class, 'quickView'])
    ->name('products.quick-view');

Route::get('/product/{slug}', [FrontendProductController::class, 'show'])
    ->name('products.show');
/*
|--------------------------------------------------------------------------
| Products / Category
|--------------------------------------------------------------------------
*/
Route::get('/shop/category/{slug}', [FrontendProductController::class, 'category'])
    ->name('shop.category');

/*
|--------------------------------------------------------------------------
| Shopping Cart
|--------------------------------------------------------------------------
*/

Route::post('/shopping/cart', [FrontendCartController::class, 'index'])
    ->name('cart.index');

Route::get('/cart', [FrontendCartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add', [FrontendCartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/update', [FrontendCartController::class, 'update'])
    ->name('cart.update');

Route::post('/cart/remove', [FrontendCartController::class, 'remove'])
    ->name('cart.remove');

Route::post('/cart/clear', [FrontendCartController::class, 'clear'])
    ->name('cart.clear');

Route::get('/cart/sidebar', [FrontendCartController::class, 'sidebar'])
    ->name('cart.sidebar');

Route::delete('/cart/{id}', [FrontendCartController::class, 'removeCartItem'])
    ->name('cart.removeCartItem');

Route::post('/compare/toggle/{product}', [CompareController::class, 'toggle'])
    ->name('compare.toggle');

Route::get('/compare', [CompareController::class, 'index'])
    ->name('compare.index');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

/*
|--------------------------------------------------------------------------
| Checkout Locations
|--------------------------------------------------------------------------
*/

Route::get('/checkout/states/{country}', [CheckoutController::class, 'states'])
    ->name('checkout.states');

/*
|--------------------------------------------------------------------------
| Checkout Shipping Zones
|--------------------------------------------------------------------------
*/

Route::get(
    '/checkout/shipping-zones/{stateId}',
    [CheckoutController::class, 'shippingZones']
)->name('checkout.shipping-zones');

/*
|--------------------------------------------------------------------------
| Checkout Shipping Rate
|--------------------------------------------------------------------------
*/

Route::post(
    '/checkout/shipping-rate',
    [CheckoutController::class, 'shippingRate']
)->name('checkout.shipping-rate');

/*
|--------------------------------------------------------------------------
| Checkout Processing
|--------------------------------------------------------------------------
*/

Route::post(
    '/checkout/process',
    [CheckoutController::class, 'process']
)->name('checkout.process');

Route::post(
    '/checkout',
    [CheckoutController::class, 'store']
)->name('checkout.store');

/*
|--------------------------------------------------------------------------
| Paystack Callback
|--------------------------------------------------------------------------
*/

Route::get(
    '/checkout/paystack/callback',
    [CheckoutController::class, 'paystackCallback']
)->name('checkout.paystack.callback');
/*
|--------------------------------------------------------------------------
| Currency
|--------------------------------------------------------------------------
*/

Route::post('/currency/switch', [CurrencyController::class, 'switch'])
    ->name('currency.switch');

/*
|--------------------------------------------------------------------------
| Wishlist
|--------------------------------------------------------------------------
*/

Route::get('/wishlist', [WishlistController::class, 'index'])
    ->name('wishlist.index');

Route::post('/wishlist/{product}', [WishlistController::class, 'store'])
    ->name('wishlist.store');

Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])
    ->name('wishlist.toggle');

Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])
    ->name('wishlist.destroy');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated Home
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])
        ->name('home');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:Admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::resource('categories', AdminCategoryController::class);

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::resource('products', AdminProductController::class);

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        Route::resource('customers', CustomerController::class);

        /*
        |--------------------------------------------------------------------------
        | Website Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/website-settings', [WebsiteSettingController::class, 'edit'])
            ->name('website.settings');

        Route::put('/website-settings', [WebsiteSettingController::class, 'update'])
            ->name('website.settings.update');

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');

        Route::patch('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])
            ->name('orders.updatePaymentStatus');

        /*
        |--------------------------------------------------------------------------
        | Shipping Rates
        |--------------------------------------------------------------------------
        */

        Route::get('/shipping-rates', [ShippingRateController::class, 'index'])
            ->name('shipping-rates.index');

        Route::get('/shipping-rates/states/{country}', [ShippingRateController::class, 'states'])
            ->name('shipping-rates.states');

        Route::post('/shipping-rates', [ShippingRateController::class, 'store'])
            ->name('shipping-rates.store');

        Route::get('/shipping-rates/{shippingRate}/edit', [ShippingRateController::class, 'edit'])
            ->name('shipping-rates.edit');

        Route::put('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'update'])
            ->name('shipping-rates.update');

        Route::delete('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'destroy'])
            ->name('shipping-rates.destroy');
    });

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('customer')
    ->name('customer.')
    ->middleware(['auth', 'role:Customer'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::get('/my-orders', [CustomerOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])
            ->name('orders.show');

        /*
        |--------------------------------------------------------------------------
        | Addresses
        |--------------------------------------------------------------------------
        */

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
