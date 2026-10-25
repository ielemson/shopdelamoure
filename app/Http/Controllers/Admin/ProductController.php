<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\ValidatesShippingMeasurements;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ValidatesShippingMeasurements;

    public function index()
    {
        $products = Product::with(['category', 'subcategory'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $subcategories = Category::whereNotNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            // Pricing
            'price_ngn' => 'required|numeric|min:0',
            'sale_price_ngn' => 'nullable|numeric|min:0|lte:price_ngn',
            'price_usd' => 'nullable|numeric|min:0',
            'sale_price_usd' => 'nullable|numeric|min:0|lte:price_usd',
            'cost_price' => 'nullable|numeric|min:0',
            'cost_price_ngn' => 'nullable|numeric|min:0',
            // Inventory
            'quantity' => 'nullable|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            // Attributes
            'weight' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $shippingData = $this->validatedShippingMeasurements($request);
        $wholesale = $this->validatedWholesaleData($request);
        /*
    |--------------------------------------------------------------------------
    | Main Product Image
    |--------------------------------------------------------------------------
    */
        $imagePath = null;
        if ($request->hasFile('main_image')) {
            $imageName = time()
                .'_'
                .Str::slug($request->name)
                .'.'
                .$request->file('main_image')->extension();
            $request->file('main_image')->move(
                public_path('uploads/products'),
                $imageName
            );
            $imagePath = 'uploads/products/'.$imageName;
        }
        /*
    |--------------------------------------------------------------------------
    | Create Product
    |--------------------------------------------------------------------------
    */
        $product = DB::transaction(function () use ($request, $validated, $imagePath, $wholesale, $shippingData) {
            $product = Product::create(array_merge([
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'brand' => $validated['brand'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'main_image' => $imagePath,
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                // Currency pricing
                'price_ngn' => $validated['price_ngn'],
                'sale_price_ngn' => $validated['sale_price_ngn'] ?? null,
                'price_usd' => $validated['price_usd'] ?? null,
                'sale_price_usd' => $validated['sale_price_usd'] ?? null,
                'cost_price_ngn' => $validated['cost_price_ngn'] ?? $validated['cost_price'] ?? 0,
                // Inventory
                'quantity' => $validated['quantity'] ?? 0,
                'low_stock_alert' => $validated['low_stock_alert'] ?? 5,
                'stock_status' => $validated['stock_status'],
                // Basic attributes
                'weight' => $validated['weight'] ?? null,
                'size' => $validated['size'] ?? null,
                'color' => $validated['color'] ?? null,
                'material' => $validated['material'] ?? null,
                'model' => $validated['model'] ?? null,
                // Product flags
                'is_featured' => $request->boolean('is_featured'),
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                // SEO
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
                'status' => $request->boolean('status'),
                'sort_order' => $validated['sort_order'] ?? 0,
            ], $wholesale['attributes'] ?? [], $shippingData));
            if ($wholesale !== null) {
                $this->syncWholesalePriceTiers($product, $wholesale['tiers']);
            }

            return $product;
        });
        /*
    |--------------------------------------------------------------------------
    | Product Gallery
    |--------------------------------------------------------------------------
    */
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $image) {
                $galleryImageName = time()
                    .'_'
                    .$index
                    .'_'
                    .Str::slug($request->name)
                    .'.'
                    .$image->extension();
                $image->move(
                    public_path('uploads/products/gallery'),
                    $galleryImageName
                );
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'uploads/products/gallery/'.$galleryImageName,
                    'sort_order' => $index,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load('wholesalePriceTiers');
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $subcategories = Category::whereNotNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'subcategory_id' => [
                'nullable',
                'exists:categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:products,name,'.$product->id,
            ],
            'brand' => [
                'nullable',
                'string',
                'max:255',
            ],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,sku,'.$product->id,
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,barcode,'.$product->id,
            ],
            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */
            'main_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:2048',
            ],
            'product_images' => [
                'nullable',
                'array',
            ],
            'product_images.*' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:2048',
            ],
            'remove_images' => [
                'nullable',
                'array',
            ],
            'remove_images.*' => [
                'exists:product_images,id',
            ],
            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */
            'short_description' => [
                'nullable',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            |
            | NGN is the base currency.
            | USD may be manually supplied where required.
            |
            */
            'cost_price_ngn' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'price_ngn' => [
                'required',
                'numeric',
                'min:0',
            ],
            'sale_price_ngn' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price_ngn',
            ],
            'price_usd' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'sale_price_usd' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */
            'quantity' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'low_stock_alert' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'stock_status' => [
                'required',
                'in:in_stock,out_of_stock',
            ],
            /*
            |--------------------------------------------------------------------------
            | Attributes
            |--------------------------------------------------------------------------
            */
            'weight' => [
                'nullable',
                'string',
                'max:255',
            ],
            'size' => [
                'nullable',
                'string',
                'max:255',
            ],
            'color' => [
                'nullable',
                'string',
                'max:255',
            ],
            'material' => [
                'nullable',
                'string',
                'max:255',
            ],
            'model' => [
                'nullable',
                'string',
                'max:255',
            ],
            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */
            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta_description' => [
                'nullable',
                'string',
            ],
            'meta_keywords' => [
                'nullable',
                'string',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */
            'is_featured' => [
                'nullable',
                'boolean',
            ],
            'is_new_arrival' => [
                'nullable',
                'boolean',
            ],
            'is_best_seller' => [
                'nullable',
                'boolean',
            ],
            'is_trending' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);
        $shippingData = $this->validatedShippingMeasurements($request);
        $wholesale = $this->validatedWholesaleData($request, $product);
        /*
        |--------------------------------------------------------------------------
        | Current Main Image
        |--------------------------------------------------------------------------
        */
        $imagePath = $product->main_image;
        /*
        |--------------------------------------------------------------------------
        | Remove Gallery Images
        |--------------------------------------------------------------------------
        */
        if ($request->filled('remove_images')) {
            $imagesToRemove = ProductImage::query()
                ->where('product_id', $product->id)
                ->whereIn(
                    'id',
                    $validated['remove_images']
                )
                ->get();
            foreach ($imagesToRemove as $image) {
                if (
                    $image->image
                    && File::exists(
                        public_path($image->image)
                    )
                ) {
                    File::delete(
                        public_path($image->image)
                    );
                }
                $image->delete();
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Replace Main Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('main_image')) {
            if (
                $product->main_image
                && File::exists(
                    public_path($product->main_image)
                )
            ) {
                File::delete(
                    public_path($product->main_image)
                );
            }
            $imageName =
                time()
                .'_'
                .Str::slug($validated['name'])
                .'.'
                .$request->file('main_image')->extension();
            $request->file('main_image')->move(
                public_path('uploads/products'),
                $imageName
            );
            $imagePath =
                'uploads/products/'.$imageName;
        }
        /*
        |--------------------------------------------------------------------------
        | Update Product
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use ($request, $validated, $imagePath, $wholesale, $product, $shippingData) {
            $product->update(array_merge([
                /*
                |--------------------------------------------------------------------------
                | Basic
                |--------------------------------------------------------------------------
                */
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'brand' => $validated['brand'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'main_image' => $imagePath,
                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */
                'short_description' => $validated['short_description'] ?? null,
                'description' => $validated['description'] ?? null,
                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */
                'cost_price_ngn' => $validated['cost_price_ngn'] ?? $validated['cost_price'] ?? 0,
                'price_ngn' => $validated['price_ngn'],
                'sale_price_ngn' => $validated['sale_price_ngn'] ?? null,
                'price_usd' => $validated['price_usd'] ?? null,
                'sale_price_usd' => $validated['sale_price_usd'] ?? null,
                /*
                |--------------------------------------------------------------------------
                | Inventory
                |--------------------------------------------------------------------------
                */
                'quantity' => $validated['quantity'] ?? 0,
                'low_stock_alert' => $validated['low_stock_alert'] ?? 5,
                'stock_status' => $validated['stock_status'],
                /*
                |--------------------------------------------------------------------------
                | Attributes
                |--------------------------------------------------------------------------
                */
                'weight' => $validated['weight'] ?? null,
                'size' => $validated['size'] ?? null,
                'color' => $validated['color'] ?? null,
                'material' => $validated['material'] ?? null,
                'model' => $validated['model'] ?? null,
                /*
                |--------------------------------------------------------------------------
                | Product Flags
                |--------------------------------------------------------------------------
                */
                'is_featured' => $request->boolean('is_featured'),
                'is_new_arrival' => $request->boolean('is_new_arrival'),
                'is_best_seller' => $request->boolean('is_best_seller'),
                'is_trending' => $request->boolean('is_trending'),
                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
                /*
                |--------------------------------------------------------------------------
                | Settings
                |--------------------------------------------------------------------------
                */
                'status' => $request->boolean('status'),
                'sort_order' => $validated['sort_order'] ?? 0,
            ], $wholesale['attributes'] ?? [], $shippingData));
            if ($wholesale !== null) {
                $this->syncWholesalePriceTiers($product, $wholesale['tiers']);
            }
        });
        /*
        |--------------------------------------------------------------------------
        | Add Gallery Images
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('product_images')) {
            $lastSortOrder = ProductImage::query()
                ->where(
                    'product_id',
                    $product->id
                )
                ->max('sort_order') ?? 0;
            foreach (
                $request->file('product_images') as $index => $image
            ) {
                $galleryImageName =
                    time()
                    .'_'
                    .$index
                    .'_'
                    .Str::slug($validated['name'])
                    .'.'
                    .$image->extension();
                $image->move(
                    public_path(
                        'uploads/products/gallery'
                    ),
                    $galleryImageName
                );
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'uploads/products/gallery/'
                        .$galleryImageName,
                    'sort_order' => $lastSortOrder
                        + $index
                        + 1,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    private function deleteGalleryImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }
        ProductImage::whereIn('id', $imageIds)
            ->get()
            ->each(function (ProductImage $image) {
                $this->deleteFile($image->image);
                $image->delete();
            });
    }

    private function swapMainImage(UploadedFile $file, ?string $oldPath, string $productName): string
    {
        $this->deleteFile($oldPath);
        $filename = time().'_'.Str::slug($productName).'.'.$file->extension();
        $file->move(public_path('uploads/products'), $filename);

        return 'uploads/products/'.$filename;
    }

    private function storeGalleryImages(array $files, int $productId, string $productName): void
    {
        foreach ($files as $index => $image) {
            $filename = time().'_'.$index.'_'.Str::slug($productName).'.'.$image->extension();
            $image->move(public_path('uploads/products/gallery'), $filename);
            ProductImage::create([
                'product_id' => $productId,
                'image' => 'uploads/products/gallery/'.$filename,
                'sort_order' => $index,
                'is_primary' => false,
            ]);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    public function destroy(Product $product)
    {
        if ($product->main_image && File::exists(public_path($product->main_image))) {
            File::delete(public_path($product->main_image));
        }
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Wholesale Configuration
    |--------------------------------------------------------------------------
    */
    private function validatedWholesaleData(
        Request $request,
        ?Product $product = null
    ): ?array {
        // Forms without this marker preserve existing wholesale settings.
        if (! $request->has('_wholesale_form')) {
            return null;
        }

        $data = $request->validate([
            '_wholesale_form' => ['required', 'in:1'],
            'wholesale_enabled' => ['required', 'boolean'],
            'wholesale_min_quantity' => [
                'required', 'integer', 'min:1', 'max:4294967295',
            ],
            'wholesale_allow_mixed_variants' => ['required', 'boolean'],
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
        $hasVariants = (bool) ($product?->has_variants ?? false);
        $minimum = (int) $data['wholesale_min_quantity'];
        $basePrice = $data['wholesale_price_ngn'] ?? null;
        $tiers = $data['wholesale_tiers'] ?? [];

        if ($hasVariants) {
            if ($basePrice !== null || count($tiers) > 0) {
                throw ValidationException::withMessages([
                    'wholesale_price_ngn' => 'Set wholesale prices and tiers on individual variants.',
                ]);
            }

            // Check this even when disabled, so saved configuration stays valid.
            if ($product->variants()->whereHas(
                'wholesalePriceTiers',
                function ($query) use ($minimum) {
                    $query->where('min_quantity', '<=', $minimum);
                }
            )->exists()) {
                throw ValidationException::withMessages([
                    'wholesale_min_quantity' => 'Variant tier quantities must exceed the product minimum. '
                        .'Update the variant tiers before raising this minimum.',
                ]);
            }
        } else {
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
                        "wholesale_tiers.$index.min_quantity" => 'Tier quantities must exceed the minimum wholesale quantity.',
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
        }

        return [
            'attributes' => [
                'wholesale_enabled' => $enabled,
                'wholesale_min_quantity' => $minimum,
                'wholesale_allow_mixed_variants' => $hasVariants
                    && $request->boolean('wholesale_allow_mixed_variants'),
                'wholesale_price_ngn' => $hasVariants ? null : $basePrice,
            ],
            'tiers' => $hasVariants ? [] : collect($tiers)
                ->sortBy(fn ($tier) => (int) $tier['min_quantity'])
                ->values()
                ->all(),
        ];
    }

    /** Convert validated decimal prices to integer kobo for exact comparisons. */
    private function wholesalePriceInKobo(string|int|float $price): int
    {
        $parts = explode('.', (string) $price, 2);
        $fraction = str_pad($parts[1] ?? '', 2, '0');

        return ((int) $parts[0] * 100) + (int) $fraction;
    }

    /** Call inside the product database transaction. */
    private function syncWholesalePriceTiers(Product $product, array $tiers): void
    {
        $quantities = array_map(
            fn ($tier) => (int) $tier['min_quantity'],
            $tiers
        );

        if (empty($quantities)) {
            $product->wholesalePriceTiers()->delete();

            return;
        }

        $product->wholesalePriceTiers()
            ->whereNotIn('min_quantity', $quantities)
            ->delete();

        foreach ($tiers as $tier) {
            $product->wholesalePriceTiers()->updateOrCreate(
                ['min_quantity' => (int) $tier['min_quantity']],
                ['price_ngn' => $tier['price_ngn']]
            );
        }
    }
}
