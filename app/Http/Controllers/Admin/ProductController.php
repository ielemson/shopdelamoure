<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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
    //     public function store(Request $request)
    // {
    //     $request->validate([
    //         'category_id' => 'required|exists:categories,id',
    //         'subcategory_id' => 'nullable|exists:categories,id',
    //         'name' => 'required|string|max:255|unique:products,name',
    //         'brand' => 'nullable|string|max:255',
    //         'sku' => 'nullable|string|max:255|unique:products,sku',
    //         'barcode' => 'nullable|string|max:255',

    //         'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
    //         'product_images' => 'nullable|array',
    //         'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',

    //         'short_description' => 'nullable|string',
    //         'description' => 'nullable|string',
    //         'cost_price' => 'nullable|numeric|min:0',
    //         'regular_price' => 'required|numeric|min:0',
    //         'sale_price' => 'nullable|numeric|min:0',
    //         'quantity' => 'nullable|integer|min:0',
    //         'low_stock_alert' => 'nullable|integer|min:0',
    //         'stock_status' => 'required|in:in_stock,out_of_stock',
    //         'weight' => 'nullable|string|max:255',
    //         'size' => 'nullable|string|max:255',
    //         'color' => 'nullable|string|max:255',
    //         'material' => 'nullable|string|max:255',
    //         'model' => 'nullable|string|max:255',
    //         'meta_title' => 'nullable|string|max:255',
    //         'meta_description' => 'nullable|string',
    //         'meta_keywords' => 'nullable|string',
    //         'sort_order' => 'nullable|integer',
    //     ]);

    //     $imagePath = null;

    //     if ($request->hasFile('main_image')) {
    //         $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->main_image->extension();

    //         $request->main_image->move(public_path('uploads/products'), $imageName);

    //         $imagePath = 'uploads/products/' . $imageName;
    //     }

    //     $product = Product::create([
    //         'category_id' => $request->category_id,
    //         'subcategory_id' => $request->subcategory_id,
    //         'name' => $request->name,
    //         'slug' => Str::slug($request->name),
    //         'brand' => $request->brand,
    //         'sku' => $request->sku,
    //         'barcode' => $request->barcode,
    //         'main_image' => $imagePath,
    //         'short_description' => $request->short_description,
    //         'description' => $request->description,
    //         'cost_price' => $request->cost_price ?? 0,
    //         'regular_price' => $request->regular_price,
    //         'sale_price' => $request->sale_price,
    //         'quantity' => $request->quantity ?? 0,
    //         'low_stock_alert' => $request->low_stock_alert ?? 5,
    //         'stock_status' => $request->stock_status,
    //         'weight' => $request->weight,
    //         'size' => $request->size,
    //         'color' => $request->color,
    //         'material' => $request->material,
    //         'model' => $request->model,
    //         'is_featured' => $request->has('is_featured'),
    //         'is_new_arrival' => $request->has('is_new_arrival'),
    //         'is_best_seller' => $request->has('is_best_seller'),
    //         'is_trending' => $request->has('is_trending'),
    //         'meta_title' => $request->meta_title,
    //         'meta_description' => $request->meta_description,
    //         'meta_keywords' => $request->meta_keywords,
    //         'status' => $request->has('status'),
    //         'sort_order' => $request->sort_order ?? 0,
    //     ]);

    //     if ($request->hasFile('product_images')) {
    //         foreach ($request->file('product_images') as $index => $image) {
    //             $galleryImageName = time() . '_' . $index . '_' . Str::slug($request->name) . '.' . $image->extension();

    //             $image->move(public_path('uploads/products/gallery'), $galleryImageName);

    //             ProductImage::create([
    //                 'product_id' => $product->id,
    //                 'image' => 'uploads/products/gallery/' . $galleryImageName,
    //                 'sort_order' => $index,
    //                 'is_primary' => false,
    //             ]);
    //         }
    //     }

    //     return redirect()
    //         ->route('admin.products.index')
    //         ->with('success', 'Product created successfully.');
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',

            'name' => 'required|string|max:255|unique:products,name',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255',

            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',

            'product_images' => 'nullable|array',
            'product_images.*' => 'image|mimes:jpg,jpeg,png,webp,avif|max:2048',

            'short_description' => 'nullable|string',
            'description' => 'nullable|string',

            // Pricing
            'price_ngn' => 'required|numeric|min:0',
            'sale_price_ngn' => 'nullable|numeric|min:0|lte:price_ngn',

            'price_usd' => 'required|numeric|min:0',
            'sale_price_usd' => 'nullable|numeric|min:0|lte:price_usd',

            'cost_price' => 'nullable|numeric|min:0',

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

        $product = Product::create([
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

            'price_usd' => $validated['price_usd'],

            'sale_price_usd' => $validated['sale_price_usd'] ?? null,

            'cost_price' => $validated['cost_price'] ?? 0,

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
        ]);

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

        $product->update([

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

            'cost_price' => $validated['cost_price'] ?? 0,

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
        ]);

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
}
