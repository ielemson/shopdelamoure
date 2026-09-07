<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WebsiteSetting;
use App\Models\Category;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    $setting = WebsiteSetting::first();
    view()->share('setting', $setting);

    

view()->share('footerCategories', Category::where('status', 1)
    ->whereNull('parent_id')
    ->orderBy('sort_order')
    ->take(5)
    ->get());
    }
}
