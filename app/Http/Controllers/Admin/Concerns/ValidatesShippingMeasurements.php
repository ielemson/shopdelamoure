<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\Request;

trait ValidatesShippingMeasurements
{
    private function validatedShippingMeasurements(Request $request): array
    {
        // Product forms save item weight only. Shipment packaging is separate.
        // Older forms without this input must preserve the saved weight.
        if (! array_key_exists('shipping_weight_kg', $request->all())) {
            return [];
        }

        $data = $request->validate([
            'shipping_weight_kg' => [
                'bail', 'nullable', 'numeric', 'decimal:0,3',
                'min:0.001', 'max:9999999.999',
            ],
        ]);

        // A deliberately blank input clears the weight; omitted input preserves it.
        if (! $request->filled('shipping_weight_kg')) {
            $data['shipping_weight_kg'] = null;
        }

        return $data;
    }
}
