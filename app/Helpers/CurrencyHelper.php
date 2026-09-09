<?php

use App\Services\CurrencyService;

if (!function_exists('currency')) {

    function currency(
        $amount,
        ?string $currency = null
    ): string {

        return app(CurrencyService::class)
            ->format(
                (float) $amount,
                $currency
            );
    }
}


if (!function_exists('currency_value')) {

    function currency_value(
        $amount,
        ?string $currency = null
    ): float {

        return app(CurrencyService::class)
            ->convert(
                (float) $amount,
                $currency
            );
    }
}


if (!function_exists('currency_rate')) {

    function currency_rate(): float
    {
        return app(CurrencyService::class)
            ->usdToNgnRate();
    }
}
