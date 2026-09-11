<?php

use App\Http\Middleware\DetectCurrency;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | Middleware Aliases
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,

            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,

            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Currency Detection
        |--------------------------------------------------------------------------
        |
        | Runs on all web requests.
        |
        | Nigeria       => NGN
        | Outside NG    => USD
        | Manual choice => respected
        |
        */

        $middleware->web(
            append: [
                DetectCurrency::class,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Paystack Webhook CSRF Exception
        |--------------------------------------------------------------------------
        |
        | Paystack cannot provide Laravel's browser CSRF token.
        |
        | This route remains protected by Paystack's HMAC SHA512
        | signature verification inside PaystackWebhookController.
        |
        */

        $middleware->validateCsrfTokens(
            except: [
                'paystack/webhook',
            ]
        );

    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
