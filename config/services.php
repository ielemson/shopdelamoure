<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'payment_url' => env(
            'PAYSTACK_PAYMENT_URL',
            'https://api.paystack.co'
        ),
    ],

    'whatsapp' => [
        'number' => env('DELAMOURE_WHATSAPP_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Conversion
    |--------------------------------------------------------------------------
    */

    'currency' => [

        /*
        |--------------------------------------------------------------------------
        | CurrencyBeacon
        |--------------------------------------------------------------------------
        */

        'currencybeacon' => [

            'key' => env('CURRENCYBEACON_API_KEY'),

            'url' => env(
                'CURRENCYBEACON_API_URL',
                'https://api.currencybeacon.com/v1'
            ),

        ],

        /*
        |--------------------------------------------------------------------------
        | Frankfurter / CBN Fallback
        |--------------------------------------------------------------------------
        */

        'frankfurter' => [

            'url' => env(
                'FRANKFURTER_API_URL',
                'https://api.frankfurter.dev/v2'
            ),

        ],

        /*
        |--------------------------------------------------------------------------
        | Cache Duration
        |--------------------------------------------------------------------------
        */

        'cache_hours' => (int) env(
            'CURRENCY_CACHE_HOURS',
            6
        ),

        /*
        |--------------------------------------------------------------------------
        | Emergency USD / NGN Rate
        |--------------------------------------------------------------------------
        */

        'fallback_rate' => (float) env(
            'USD_NGN_FALLBACK_RATE',
            1500
        ),

        /*
        |--------------------------------------------------------------------------
        | Optional USD Price Buffer
        |--------------------------------------------------------------------------
        */

        'usd_buffer_percent' => (float) env(
            'USD_PRICE_BUFFER_PERCENT',
            0
        ),

    ],

];
