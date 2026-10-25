<?php

/*
|--------------------------------------------------------------------------
| Framework
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeliverySettingController;
/*
|--------------------------------------------------------------------------
| General Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ShippingPackageController;
use App\Http\Controllers\Admin\ShippingRateController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CheckoutCouponController;
/*
|--------------------------------------------------------------------------
| Frontend Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CompareController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Frontend\CartController as FrontendCartController;
/*
|--------------------------------------------------------------------------
| Customer Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Frontend\ContactController;
/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Frontend\CurrencyController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\WholesaleController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/product/quick-view/{product:slug}', [FrontendProductController::class, 'quickView'])->name('products.quick-view');
Route::get('/product/{slug}', [FrontendProductController::class, 'show'])->name('products.show');
Route::get('/shop/category/{slug}', [FrontendProductController::class, 'category'])->name('shop.category');

/*
|--------------------------------------------------------------------------
| Wholesale
|--------------------------------------------------------------------------
*/

Route::get('/shop/wholesale', [WholesaleController::class, 'index'])
    ->name('wholesale.index');

Route::post('/shop/wholesale/cart/add', [
    FrontendCartController::class,
    'addWholesale',
])->name('wholesale.cart.add');

Route::get('/shop/wholesale/{product:slug}', [
    WholesaleController::class,
    'show',
])->name('wholesale.show');

/*
|--------------------------------------------------------------------------
| Shopping Cart
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Product Comparison
|--------------------------------------------------------------------------
*/

Route::post('/compare/toggle/{product}', [CompareController::class, 'toggle'])
    ->name('compare.toggle');

Route::get('/compare', [CompareController::class, 'index'])
    ->name('compare.index');

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
| Checkout / Locations / Shipping
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::get('/checkout/states/{country}', [CheckoutController::class, 'states'])
    ->name('checkout.states');

Route::get('/checkout/shipping-zones/{stateId}', [
    CheckoutController::class,
    'shippingZones',
])->name('checkout.shipping-zones');

Route::post('/checkout/shipping-rate', [
    CheckoutController::class,
    'shippingRate',
])->name('checkout.shipping-rate');

/*
|--------------------------------------------------------------------------
| Checkout Processing
|--------------------------------------------------------------------------
*/

Route::post('/checkout/process', [CheckoutController::class, 'process'])
    ->name('checkout.process');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store');

/*
|--------------------------------------------------------------------------
| Paystack
|--------------------------------------------------------------------------
*/

Route::get('/checkout/paystack/callback', [
    CheckoutController::class,
    'paystackCallback',
])->name('checkout.paystack.callback');

Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handle'])
    ->name('paystack.webhook');

/*
|--------------------------------------------------------------------------
| Checkout Coupons
|--------------------------------------------------------------------------
*/

Route::post('/coupon/apply', [CheckoutCouponController::class, 'apply'])
    ->name('coupon.apply');

Route::post('/coupon/remove', [CheckoutCouponController::class, 'remove'])
    ->name('coupon.remove');

/*
|--------------------------------------------------------------------------
| Currency
|--------------------------------------------------------------------------
*/

Route::post('/currency/switch', [CurrencyController::class, 'switch'])
    ->name('currency.switch');

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

        Route::get('/shipping-packages', [ShippingPackageController::class, 'index'])
            ->name('shipping-packages.index');
        Route::post('/shipping-packages', [ShippingPackageController::class, 'store'])
            ->name('shipping-packages.store');
        Route::put('/shipping-packages/{shippingPackage}', [ShippingPackageController::class, 'update'])
            ->name('shipping-packages.update');
        Route::post('/shipping-packages/quote', [ShippingPackageController::class, 'quote'])
            ->middleware('throttle:3,1')->name('shipping-packages.quote');

        Route::get('/shipping-packages/cities', [ShippingPackageController::class, 'cities',
        ])->middleware('throttle:10,1')->name('shipping-packages.cities');

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
        | Product Variants
        |--------------------------------------------------------------------------
        */

        Route::get('products/{product}/variants', [
            ProductVariantController::class,
            'index',
        ])->name('products.variants.index');

        Route::get('products/{product}/variants/create', [
            ProductVariantController::class,
            'create',
        ])->name('products.variants.create');

        Route::post('products/{product}/variants', [
            ProductVariantController::class,
            'store',
        ])->name('products.variants.store');

        Route::get('products/{product}/variants/{variant}/edit', [
            ProductVariantController::class,
            'edit',
        ])->name('products.variants.edit');

        Route::put('products/{product}/variants/{variant}', [
            ProductVariantController::class,
            'update',
        ])->name('products.variants.update');

        Route::delete('products/{product}/variants/{variant}', [
            ProductVariantController::class,
            'destroy',
        ])->name('products.variants.destroy');

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

        Route::patch('/orders/{order}/status', [
            OrderController::class,
            'updateStatus',
        ])->name('orders.updateStatus');

        Route::patch('/orders/{order}/payment-status', [
            OrderController::class,
            'updatePaymentStatus',
        ])->name('orders.updatePaymentStatus');

        Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])
            ->name('orders.refund');

        Route::patch('/orders/{order}/return', [OrderController::class, 'updateReturn'])
            ->name('orders.return.update');

        /*
        |--------------------------------------------------------------------------
        | Delivery Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/delivery-settings', [
            DeliverySettingController::class,
            'edit',
        ])->name('delivery.settings');

        Route::put('/delivery-settings', [
            DeliverySettingController::class,
            'update',
        ])->name('delivery.settings.update');

        Route::post('/delivery-settings/test-connection', [
            DeliverySettingController::class,
            'testConnection',
        ])->middleware('throttle:5,1')
            ->name('delivery.settings.test');

        /*
        |--------------------------------------------------------------------------
        | Shipping Rates
        |--------------------------------------------------------------------------
        */

        Route::get('/shipping-rates', [ShippingRateController::class, 'index'])
            ->name('shipping-rates.index');

        Route::get('/shipping-rates/states/{country}', [
            ShippingRateController::class,
            'states',
        ])->name('shipping-rates.states');

        Route::post('/shipping-rates', [ShippingRateController::class, 'store'])
            ->name('shipping-rates.store');

        Route::get('/shipping-rates/{shippingRate}/edit', [
            ShippingRateController::class,
            'edit',
        ])->name('shipping-rates.edit');

        Route::put('/shipping-rates/{shippingRate}', [
            ShippingRateController::class,
            'update',
        ])->name('shipping-rates.update');

        Route::delete('/shipping-rates/{shippingRate}', [
            ShippingRateController::class,
            'destroy',
        ])->name('shipping-rates.destroy');

        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        Route::prefix('inventory')
            ->name('inventory.')
            ->group(function () {

                Route::get('/', [InventoryController::class, 'index'])
                    ->name('index');

                Route::get('/low-stock', [InventoryController::class, 'lowStock'])
                    ->name('low-stock');

                Route::get('/movements', [InventoryController::class, 'movements'])
                    ->name('movements');

                Route::post('/adjust', [InventoryController::class, 'adjust'])
                    ->name('adjust');
            });

        /*
        |--------------------------------------------------------------------------
        | Coupons
        |--------------------------------------------------------------------------
        */

        Route::resource('coupons', CouponController::class)
            ->except('show');

        Route::patch('coupons/{coupon}/toggle-status', [
            CouponController::class,
            'toggleStatus',
        ])->name('coupons.toggle-status');

        require __DIR__.'/terminal-checkout-admin.php';

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

        Route::get('/addresses/{address}/edit', [
            CustomerAddressController::class,
            'edit',
        ])->name('addresses.edit');

        Route::put('/addresses/{address}', [
            CustomerAddressController::class,
            'update',
        ])->name('addresses.update');

        Route::delete('/addresses/{address}', [
            CustomerAddressController::class,
            'destroy',
        ])->name('addresses.destroy');

        /*
        |--------------------------------------------------------------------------
        | Profile / Password
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');
    });

require __DIR__.'/terminal-checkout.php';
