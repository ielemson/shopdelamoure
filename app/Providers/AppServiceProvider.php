<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        /*
        |--------------------------------------------------------------------------
        | Website Settings
        |--------------------------------------------------------------------------
        */

        $setting = null;

        if (Schema::hasTable('website_settings')) {
            $setting = WebsiteSetting::first();
        }

        view()->share('setting', $setting);


        /*
        |--------------------------------------------------------------------------
        | Footer Categories
        |--------------------------------------------------------------------------
        */

        $footerCategories = collect();

        if (Schema::hasTable('categories')) {
            $footerCategories = Category::where('status', 1)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->take(5)
                ->get();
        }

        view()->share('footerCategories', $footerCategories);
    }
}
