<?php

namespace App\Http\Middleware;

use Closure;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gateway notifications do not need visitor currency detection.
        if ($request->routeIs('paystack.webhook')) {
            return $next($request);
        }

        // Wholesale prices are currently configured only in NGN. This takes
        // precedence over both manual selection and automatic country detection.
        $hasWholesale = Cart::getContent()->contains(
            fn ($item) => $item->attributes->get('purchase_type') === 'wholesale'
        );
        $request->attributes->set('wholesale_currency_locked', $hasWholesale);
        if ($hasWholesale) {
            session(['currency' => 'NGN']);

            return $next($request);
        }

        // Respect manual currency selection for carts without wholesale items.
        if (session()->get('currency_manual') === true) {
            return $next($request);
        }

        // Detect only once per session.
        if (session()->has('currency_detected')) {
            return $next($request);
        }

        $countryCode = $this->detectCountry($request);
        $currency = match (strtoupper((string) $countryCode)) {
            'NG' => 'NGN',
            '' => 'NGN',
            default => 'USD',
        };
        session([
            'currency' => $currency,
            'currency_detected' => true,
            'visitor_country' => $countryCode,
        ]);

        return $next($request);
    }

    private function detectCountry(Request $request): ?string
    {
        $cloudflareCountry = $request->header('CF-IPCountry');
        if ($cloudflareCountry && ! in_array(strtoupper($cloudflareCountry), ['XX', 'T1'])) {
            return strtoupper($cloudflareCountry);
        }

        $headers = [
            'CloudFront-Viewer-Country',
            'X-Country-Code',
            'X-Country',
            'X-AppEngine-Country',
        ];
        foreach ($headers as $header) {
            $country = $request->header($header);
            if ($country) {
                return strtoupper(trim($country));
            }
        }

        $ip = $request->ip();
        if (! $ip || $ip === '127.0.0.1' || $ip === '::1'
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'NG';
        }

        $cacheKey = 'visitor_country_'.md5($ip);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($ip) {
            return $this->lookupIpCountry($ip);
        });
    }

    private function lookupIpCountry(string $ip): ?string
    {
        try {
            $response = Http::acceptJson()->timeout(5)->retry(2, 200)
                ->get('https://ipwho.is/'.urlencode($ip));
            if (! $response->successful()) {
                Log::warning('Currency country detection request failed.', ['status' => $response->status()]);

                return null;
            }
            if ($response->json('success') === false) {
                return null;
            }
            $countryCode = $response->json('country_code');
            if (! $countryCode) {
                return null;
            }

            return strtoupper($countryCode);
        } catch (\Throwable $e) {
            Log::warning('Currency country detection exception.', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
