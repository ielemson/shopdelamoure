<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Respect Manual Currency Selection
        |--------------------------------------------------------------------------
        |
        | If the customer has manually selected NGN or USD, automatic
        | country detection must never override that selection.
        |
        */

        if (session()->get('currency_manual') === true) {

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Detect Only Once Per Session
        |--------------------------------------------------------------------------
        */

        if (session()->has('currency_detected')) {

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Detect Visitor Country
        |--------------------------------------------------------------------------
        */

        $countryCode = $this->detectCountry($request);

        /*
        |--------------------------------------------------------------------------
        | 4. Determine Currency
        |--------------------------------------------------------------------------
        |
        | Nigeria         => NGN
        | Outside Nigeria => USD
        | Unknown         => NGN
        |
        */

        $currency = match (strtoupper((string) $countryCode)) {

            'NG' => 'NGN',

            '' => 'NGN',

            default => 'USD',

        };

        /*
        |--------------------------------------------------------------------------
        | 5. Save Detection In Session
        |--------------------------------------------------------------------------
        */

        session([
            'currency' => $currency,
            'currency_detected' => true,
            'visitor_country' => $countryCode,
        ]);

        return $next($request);
    }

    /**
     * Detect visitor country.
     */
    private function detectCountry(Request $request): ?string
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Cloudflare
        |--------------------------------------------------------------------------
        |
        | If Cloudflare is being used, this is the preferred method.
        |
        */

        $cloudflareCountry = $request->header('CF-IPCountry');

        if (
            $cloudflareCountry &&
            ! in_array(
                strtoupper($cloudflareCountry),
                ['XX', 'T1']
            )
        ) {

            return strtoupper($cloudflareCountry);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Other Common Hosting Headers
        |--------------------------------------------------------------------------
        */

        $headers = [

            'CloudFront-Viewer-Country',

            'X-Country-Code',

            'X-Country',

            'X-AppEngine-Country',

        ];

        foreach ($headers as $header) {

            $country = $request->header($header);

            if ($country) {

                return strtoupper(
                    trim($country)
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | 3. Visitor IP Address
        |--------------------------------------------------------------------------
        */

        $ip = $request->ip();

        /*
        |--------------------------------------------------------------------------
        | Local / Private IP
        |--------------------------------------------------------------------------
        |
        | During localhost development there is no useful public IP.
        | Default to Nigeria.
        |
        */

        if (
            ! $ip ||
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false
        ) {

            return 'NG';
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Cache Country Lookup
        |--------------------------------------------------------------------------
        |
        | Avoid calling the geolocation service repeatedly for the same IP.
        |
        */

        $cacheKey = 'visitor_country_'.md5($ip);

        return Cache::remember(
            $cacheKey,
            now()->addDay(),
            function () use ($ip) {

                return $this->lookupIpCountry($ip);

            }
        );
    }

    /**
     * IP geolocation fallback.
     */
    private function lookupIpCountry(string $ip): ?string
    {
        try {

            $response = Http::acceptJson()
                ->timeout(5)
                ->retry(2, 200)
                ->get(
                    'https://ipwho.is/'.urlencode($ip)
                );

            if (! $response->successful()) {

                Log::warning(
                    'Currency country detection request failed.',
                    [
                        'status' => $response->status(),
                    ]
                );

                return null;
            }

            if ($response->json('success') === false) {

                return null;
            }

            $countryCode = $response->json(
                'country_code'
            );

            if (! $countryCode) {

                return null;
            }

            return strtoupper(
                $countryCode
            );

        } catch (\Throwable $e) {

            Log::warning(
                'Currency country detection exception.',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return null;
        }
    }
}
