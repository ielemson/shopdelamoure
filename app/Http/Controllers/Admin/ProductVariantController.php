<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $variants = $product->variants()
            ->with('wholesalePriceTiers')
            ->get();

        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        return view('admin.products.variants.create', compact('product'));
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        $this->ensureOwnership($product, $variant);
        $variant->load('wholesalePriceTiers');

        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function store(Request $request, Product $product)
    {
        // Validate before uploading files.
        $data = $this->validatedData($request, $product);
        $this->validatedWholesaleData($request, $product);
        $newImage = $this->uploadVariantImage($request);

        if ($newImage) {
            $data['image'] = $newImage;
        }

        try {
            DB::transaction(function () use ($request, $product, $data) {
                $lockedProduct = Product::whereKey($product->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Recheck against the current minimum under the product lock.
                $wholesale = $this->validatedWholesaleData($request, $lockedProduct);
                $variantData = array_merge($data, $wholesale['attributes'] ?? []);

                if ($variantData['is_default']) {
                    $product->variants()->update(['is_default' => false]);
                }

                $variant = $product->variants()->create($variantData);

                if ($wholesale !== null) {
                    $this->syncWholesalePriceTiers($variant, $wholesale['tiers']);
                }

                $product->update(['has_variants' => true]);
                $this->ensureActiveDefault($product, $variant);
            });
        } catch (Throwable $e) {
            $this->deleteVariantImage($newImage);
            throw $e;
        }

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Variant saved successfully.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $this->ensureOwnership($product, $variant);

        $data = $this->validatedData($request, $product, $variant);
        $this->validatedWholesaleData($request, $product);
        $oldImage = $variant->image;
        $removeImage = $request->boolean('remove_image');
        $newImage = $this->uploadVariantImage($request);

        // A replacement upload takes precedence over the remove checkbox.
        if ($newImage) {
            $data['image'] = $newImage;
        } elseif ($removeImage) {
            $data['image'] = null;
        }

        try {
            DB::transaction(function () use ($request, $product, $variant, $data) {
                $lockedProduct = Product::whereKey($product->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $wholesale = $this->validatedWholesaleData($request, $lockedProduct);
                $variantData = array_merge($data, $wholesale['attributes'] ?? []);

                if ($variantData['is_default']) {
                    $product->variants()->update(['is_default' => false]);
                }

                $variant->update($variantData);

                if ($wholesale !== null) {
                    $this->syncWholesalePriceTiers($variant, $wholesale['tiers']);
                }

                $this->ensureActiveDefault($product, $variant);
            });
        } catch (Throwable $e) {
            $this->deleteVariantImage($newImage);
            throw $e;
        }

        // Keep the old file if the database save fails.
        if ($newImage || $removeImage) {
            $this->deleteVariantImage($oldImage);
        }

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->ensureOwnership($product, $variant);
        $image = $variant->image;

        DB::transaction(function () use ($product, $variant) {
            Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            // The tier foreign key cascades when the variant is deleted.
            $variant->delete();

            if (! $product->variants()->exists()) {
                $product->update(['has_variants' => false]);
            } else {
                $this->ensureActiveDefault($product);
            }
        });

        $this->deleteVariantImage($image);

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Variant deleted successfully.');
    }

    private function ensureOwnership(Product $product, ProductVariant $variant): void
    {
        abort_unless((int) $variant->product_id === (int) $product->id, 404);
    }

    private function ensureActiveDefault(
        Product $product,
        ?ProductVariant $preferred = null
    ): void {
        $activeDefault = $product->variants()
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        if ($activeDefault) {
            return;
        }

        $product->variants()->update(['is_default' => false]);

        $next = $preferred?->is_active
            ? $preferred
            : $product->variants()->where('is_active', true)->first();

        $next?->update(['is_default' => true]);
    }

    private function validatedData(
        Request $request,
        Product $product,
        ?ProductVariant $variant = null
    ): array {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable', 'string', 'max:255',
                Rule::unique('product_variants', 'sku')->ignore($variant?->id),
            ],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],

            'option_keys' => ['nullable', 'array', 'max:10'],
            'option_keys.*' => ['nullable', 'string', 'max:100'],
            'option_values' => ['nullable', 'array', 'max:10'],
            'option_values.*' => ['nullable', 'string', 'max:255'],

            'price_ngn' => ['nullable', 'numeric', 'min:0'],
            'sale_price_ngn' => ['nullable', 'numeric', 'min:0'],
            'price_usd' => ['nullable', 'numeric', 'min:0'],
            'sale_price_usd' => ['nullable', 'numeric', 'min:0'],

            'track_stock' => ['required', 'boolean'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_status' => ['required', Rule::in(['in_stock', 'out_of_stock'])],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'is_default' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $options = [];

        foreach ($data['option_keys'] ?? [] as $index => $key) {
            $key = trim((string) $key);
            $value = trim((string) ($data['option_values'][$index] ?? ''));

            if ($key === '' && $value === '') {
                continue;
            }

            if ($key === '' || $value === '') {
                throw ValidationException::withMessages([
                    'option_keys' => 'Every option needs both a name and a value.',
                ]);
            }

            if (array_key_exists($key, $options)) {
                throw ValidationException::withMessages([
                    'option_keys' => "The option '{$key}' appears more than once.",
                ]);
            }

            $options[$key] = $value;
        }

        $data['options'] = $options ?: null;
        unset($data['option_keys'], $data['option_values'], $data['remove_image']);
        // File objects must not be mass-assigned to the image path column.
        unset($data['image']);

        if ($data['is_default'] && ! $data['is_active']) {
            throw ValidationException::withMessages([
                'is_default' => 'An inactive variant cannot be the default.',
            ]);
        }

        foreach (['ngn' => 'NGN', 'usd' => 'USD'] as $suffix => $currency) {
            $regular = $data["price_{$suffix}"]
                ?? $product->getRegularPrice($currency);
            $sale = $data["sale_price_{$suffix}"] ?? null;

            if ($sale !== null && ($regular === null || $sale >= $regular)) {
                throw ValidationException::withMessages([
                    "sale_price_{$suffix}" => "The {$currency} sale price must be below the regular price.",
                ]);
            }
        }

        if ($data['track_stock']) {
            $data['stock_status'] = $data['stock_quantity'] > 0
                ? 'in_stock'
                : 'out_of_stock';
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Wholesale Validation
    |--------------------------------------------------------------------------
    | The form submits _wholesale_form=1 plus explicit boolean values.
    | Without that marker, existing wholesale settings and tiers are preserved.
    | A variant can be configured before the parent enables wholesale.
    */
    private function validatedWholesaleData(Request $request, Product $product): ?array
    {
        if (! $request->has('_wholesale_form')) {
            return null;
        }

        $data = $request->validate([
            '_wholesale_form' => ['required', 'in:1'],
            'wholesale_enabled' => ['required', 'boolean'],
            'wholesale_price_ngn' => [
                'nullable', 'numeric', 'decimal:0,2',
                'regex:/^[0-9]{1,13}(\.[0-9]{1,2})?$/',
                'min:0.01', 'max:9999999999999.99',
            ],
            'wholesale_tiers' => ['nullable', 'array', 'max:50'],
            'wholesale_tiers.*' => [
                'required', 'array:min_quantity,price_ngn',
            ],
            'wholesale_tiers.*.min_quantity' => [
                'required', 'integer', 'min:1',
                'max:4294967295', 'distinct',
            ],
            'wholesale_tiers.*.price_ngn' => [
                'required', 'numeric', 'decimal:0,2',
                'regex:/^[0-9]{1,13}(\.[0-9]{1,2})?$/',
                'min:0.01', 'max:9999999999999.99',
            ],
        ]);

        $enabled = $request->boolean('wholesale_enabled');
        $basePrice = $data['wholesale_price_ngn'] ?? null;
        $tiers = $data['wholesale_tiers'] ?? [];
        $minimum = (int) $product->wholesale_min_quantity;

        if ($minimum < 1) {
            throw ValidationException::withMessages([
                'wholesale_enabled' => 'Set a valid minimum wholesale quantity on the product first.',
            ]);
        }

        if (($enabled || count($tiers) > 0) && $basePrice === null) {
            throw ValidationException::withMessages([
                'wholesale_price_ngn' => 'Enter a base wholesale unit price before enabling '
                    .'wholesale or adding tiers.',
            ]);
        }

        $sortedTiers = collect($tiers)->sortBy(
            fn ($tier) => (int) $tier['min_quantity']
        );
        $previousPrice = $basePrice !== null
            ? $this->wholesalePriceInKobo($basePrice)
            : null;

        foreach ($sortedTiers as $index => $tier) {
            if ((int) $tier['min_quantity'] <= $minimum) {
                throw ValidationException::withMessages([
                    "wholesale_tiers.$index.min_quantity" => "Tier quantities must exceed the product minimum of {$minimum} units.",
                ]);
            }

            $price = $this->wholesalePriceInKobo($tier['price_ngn']);
            if ($previousPrice !== null && $price >= $previousPrice) {
                throw ValidationException::withMessages([
                    "wholesale_tiers.$index.price_ngn" => 'Each higher quantity tier must have a lower unit price.',
                ]);
            }
            $previousPrice = $price;
        }

        return [
            'attributes' => [
                'wholesale_enabled' => $enabled,
                'wholesale_price_ngn' => $basePrice,
            ],
            'tiers' => $sortedTiers->values()->all(),
        ];
    }

    /** Convert validated decimal prices to integer kobo for exact comparisons. */
    private function wholesalePriceInKobo(string|int|float $price): int
    {
        $parts = explode('.', (string) $price, 2);
        $fraction = str_pad($parts[1] ?? '', 2, '0');

        return ((int) $parts[0] * 100) + (int) $fraction;
    }

    /** Call inside the variant database transaction. */
    private function syncWholesalePriceTiers(ProductVariant $variant, array $tiers): void
    {
        $quantities = array_map(
            fn ($tier) => (int) $tier['min_quantity'],
            $tiers
        );

        if (empty($quantities)) {
            $variant->wholesalePriceTiers()->delete();

            return;
        }

        $variant->wholesalePriceTiers()
            ->whereNotIn('min_quantity', $quantities)
            ->delete();

        foreach ($tiers as $tier) {
            $variant->wholesalePriceTiers()->updateOrCreate(
                ['min_quantity' => (int) $tier['min_quantity']],
                ['price_ngn' => $tier['price_ngn']]
            );
        }
    }

    private function uploadVariantImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('uploads/product-variants');
        File::ensureDirectoryExists($directory);

        $imageName = time()
            .'_'
            .Str::slug($request->input('name'))
            .'_'
            .Str::random(8)
            .'.'
            .$request->file('image')->extension();

        $request->file('image')->move($directory, $imageName);

        return 'uploads/product-variants/'.$imageName;
    }

    private function deleteVariantImage(?string $image): void
    {
        if (! $image || ! str_starts_with($image, 'uploads/product-variants/')) {
            return;
        }

        $path = public_path($image);

        if (is_file($path)) {
            File::delete($path);
        }
    }
}
