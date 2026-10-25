<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        | Database Compatibility
        |--------------------------------------------------------------------------
        |
        | Prevent "Specified key was too long" errors on hosting environments
        | with lower MySQL/MariaDB index limits.
        |
        */

        Schema::defaultStringLength(191);

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

        \Illuminate\Support\Facades\View::composer(
            ['layouts.admin', 'layouts.admin.*', 'admin.*'],
            function (\Illuminate\View\View $view) {
                $view->with(
                    'adminWebsiteSetting',
                    \App\Models\WebsiteSetting::query()->orderBy('id')->first()
                );
            }
        );

    }
}
