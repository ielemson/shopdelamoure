<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    private const RATE_CACHE_KEY = 'currency.usd_ngn.rate';
    private const LAST_GOOD_RATE_KEY = 'currency.usd_ngn.last_good_rate';
    private const RATE_SOURCE_KEY = 'currency.usd_ngn.source';
    private const RATE_UPDATED_KEY = 'currency.usd_ngn.updated_at';

    /**
     * Get current USD -> NGN rate.
     *
     * Example:
     * 1 USD = ₦1,500
     */
    public function usdToNgnRate(bool $forceRefresh = false): float
    {
        if ($forceRefresh) {
            Cache::forget(self::RATE_CACHE_KEY);
        }

        $hours = max(
            1,
            (int) config('services.currency.cache_hours', 6)
        );

        return (float) Cache::remember(
            self::RATE_CACHE_KEY,
            now()->addHours($hours),
            function () {

                /*
                |--------------------------------------------------------------------------
                | 1. CurrencyBeacon
                |--------------------------------------------------------------------------
                */

                $result = $this->fromCurrencyBeacon();

                /*
                |--------------------------------------------------------------------------
                | 2. Frankfurter / CBN fallback
                |--------------------------------------------------------------------------
                */

                if (!$result) {
                    $result = $this->fromFrankfurter();
                }

                /*
                |--------------------------------------------------------------------------
                | Valid API result
                |--------------------------------------------------------------------------
                */

                if ($result) {

                    $rate = (float) $result['rate'];

                    Cache::forever(
                        self::LAST_GOOD_RATE_KEY,
                        $rate
                    );

                    Cache::forever(
                        self::RATE_SOURCE_KEY,
                        $result['source']
                    );

                    Cache::forever(
                        self::RATE_UPDATED_KEY,
                        now()->toDateTimeString()
                    );

                    return $rate;
                }

                /*
                |--------------------------------------------------------------------------
                | 3. Last successfully retrieved rate
                |--------------------------------------------------------------------------
                */

                $lastGoodRate = Cache::get(
                    self::LAST_GOOD_RATE_KEY
                );

                if (
                    is_numeric($lastGoodRate) &&
                    $lastGoodRate > 0
                ) {

                    Cache::forever(
                        self::RATE_SOURCE_KEY,
                        'Last Known Rate'
                    );

                    return (float) $lastGoodRate;
                }

                /*
                |--------------------------------------------------------------------------
                | 4. Emergency fallback
                |--------------------------------------------------------------------------
                */

                $fallback = (float) config(
                    'services.currency.fallback_rate',
                    1500
                );

                Cache::forever(
                    self::RATE_SOURCE_KEY,
                    'Emergency Fallback'
                );

                return $fallback;
            }
        );
    }

    /**
     * CurrencyBeacon
     */
    private function fromCurrencyBeacon(): ?array
    {
        try {

            $apiKey = config(
                'services.currency.currencybeacon.key'
            );

            if (empty($apiKey)) {
                return null;
            }

            $baseUrl = rtrim(
                config(
                    'services.currency.currencybeacon.url',
                    'https://api.currencybeacon.com/v1'
                ),
                '/'
            );

            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(10)
                ->retry(2, 300)
                ->get($baseUrl . '/latest', [
                    'base' => 'USD',
                    'symbols' => 'NGN',
                ]);

            if (!$response->successful()) {

                Log::warning(
                    'CurrencyBeacon exchange rate request failed.',
                    [
                        'status' => $response->status(),
                    ]
                );

                return null;
            }

            $rate = data_get(
                $response->json(),
                'response.rates.NGN'
            );

            if (!$this->validRate($rate)) {
                return null;
            }

            return [
                'rate' => (float) $rate,
                'source' => 'CurrencyBeacon',
            ];
        } catch (\Throwable $e) {

            Log::warning(
                'CurrencyBeacon exchange rate exception.',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return null;
        }
    }

    /**
     * Frankfurter using the Central Bank of Nigeria.
     */
    private function fromFrankfurter(): ?array
    {
        try {

            $baseUrl = rtrim(
                config(
                    'services.currency.frankfurter.url',
                    'https://api.frankfurter.dev/v2'
                ),
                '/'
            );

            $response = Http::acceptJson()
                ->timeout(10)
                ->retry(2, 300)
                ->get(
                    $baseUrl . '/rate/USD/NGN',
                    [
                        'providers' => 'CBN',
                    ]
                );

            if (!$response->successful()) {

                Log::warning(
                    'Frankfurter exchange rate request failed.',
                    [
                        'status' => $response->status(),
                    ]
                );

                return null;
            }

            $rate = $response->json('rate');

            if (!$this->validRate($rate)) {
                return null;
            }

            return [
                'rate' => (float) $rate,
                'source' => 'Frankfurter / CBN',
            ];
        } catch (\Throwable $e) {

            Log::warning(
                'Frankfurter exchange rate exception.',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return null;
        }
    }

    /**
     * Validate API rate.
     */
    private function validRate($rate): bool
    {
        return is_numeric($rate)
            && (float) $rate > 0;
    }

    /**
     * Convert NGN database price to USD.
     */
    public function ngnToUsd(float $amount): float
    {
        $rate = $this->usdToNgnRate();

        if ($rate <= 0) {
            return 0;
        }

        $amountInUsd = $amount / $rate;

        /*
        |--------------------------------------------------------------------------
        | Optional commercial buffer
        |--------------------------------------------------------------------------
        |
        | Example:
        | USD_PRICE_BUFFER_PERCENT=2
        |
        | $100 becomes $102.
        |
        */

        $buffer = max(
            0,
            (float) config(
                'services.currency.usd_buffer_percent',
                0
            )
        );

        if ($buffer > 0) {
            $amountInUsd *= (1 + ($buffer / 100));
        }

        return round($amountInUsd, 2);
    }

    /**
     * Convert NGN amount according to visitor's selected currency.
     */
    public function convert(
        float $amount,
        ?string $currency = null
    ): float {

        $currency = strtoupper(
            $currency ?? session('currency', 'NGN')
        );

        return match ($currency) {

            'USD' => $this->ngnToUsd($amount),

            default => $amount,
        };
    }

    /**
     * Format price.
     */
    public function format(
        float $amount,
        ?string $currency = null
    ): string {

        $currency = strtoupper(
            $currency ?? session('currency', 'NGN')
        );

        $convertedAmount = $this->convert(
            $amount,
            $currency
        );

        return match ($currency) {

            'USD' => '$' . number_format(
                $convertedAmount,
                2
            ),

            default => '₦' . number_format(
                $convertedAmount,
                2
            ),
        };
    }

    /**
     * Useful for admin/debug display.
     */
    public function rateInformation(): array
    {
        return [
            'rate' => $this->usdToNgnRate(),

            'source' => Cache::get(
                self::RATE_SOURCE_KEY,
                'Unknown'
            ),

            'updated_at' => Cache::get(
                self::RATE_UPDATED_KEY
            ),
        ];
    }

    /**
     * Force an immediate API refresh.
     */
    public function refresh(): float
    {
        return $this->usdToNgnRate(true);
    }
}
