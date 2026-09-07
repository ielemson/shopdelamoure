<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Resolve Categories
        |--------------------------------------------------------------------------
        | These slugs must exist in CategorySeeder.
        */
        $fragrances = Category::where('slug', 'fragrances')->firstOrFail();
        $bodyCare = Category::where('slug', 'body-care')->firstOrFail();
        $homeFragrance = Category::where('slug', 'home-fragrance')->firstOrFail();
        $giftSets = Category::where('slug', 'gift-sets')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Resolve Optional Subcategories
        |--------------------------------------------------------------------------
        */
        $perfumes = Category::where('slug', 'perfumes')->first();
        $bodyMists = Category::where('slug', 'body-mists')->first();
        $bodyOils = Category::where('slug', 'body-oils')->first();
        $diffusers = Category::where('slug', 'reed-diffusers')->first();
        $candles = Category::where('slug', 'scented-candles')->first();

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
        $products = [

            /*
            |--------------------------------------------------------------------------
            | FRAGRANCES
            |--------------------------------------------------------------------------
            */
            [
                'category_id' => $fragrances->id,
                'subcategory_id' => $perfumes?->id,

                'name' => 'Velvet Oud Eau de Parfum',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-VOUD-001',

                'short_description' =>
                'A rich and sophisticated oud fragrance with warm woody and amber notes.',

                'description' =>
                'Velvet Oud Eau de Parfum is an elegant fragrance created for lovers of deep, luxurious scents. It combines warm oud, amber and soft woody accords for a refined and long-lasting fragrance experience.',

                'price_ngn' => 65000,
                'sale_price_ngn' => 59000,
                'price_usd' => 49.00,
                'sale_price_usd' => 44.00,
                'cost_price_ngn' => 35000,

                'has_variants' => true,

                'quantity' => 0,
                'stock_status' => 'in_stock',

                'weight' => '0.5kg',
                'material' => null,
                'model' => null,

                'is_featured' => true,
                'is_new_arrival' => false,
                'is_best_seller' => true,
                'is_trending' => true,

                'variants' => [
                    [
                        'name' => '50ml',
                        'sku' => 'DLA-VOUD-50',
                        'options' => [
                            'size' => '50ml',
                        ],
                        'price_ngn' => 45000,
                        'sale_price_ngn' => 42000,
                        'price_usd' => 34.00,
                        'sale_price_usd' => 31.00,
                        'stock_quantity' => 20,
                        'is_default' => false,
                    ],
                    [
                        'name' => '100ml',
                        'sku' => 'DLA-VOUD-100',
                        'options' => [
                            'size' => '100ml',
                        ],
                        'price_ngn' => 65000,
                        'sale_price_ngn' => 59000,
                        'price_usd' => 49.00,
                        'sale_price_usd' => 44.00,
                        'stock_quantity' => 15,
                        'is_default' => true,
                    ],
                ],
            ],

            [
                'category_id' => $fragrances->id,
                'subcategory_id' => $perfumes?->id,

                'name' => 'Rose Noir Eau de Parfum',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-RNOIR-002',

                'short_description' =>
                'A sensual floral fragrance blending rose, vanilla, musk and soft woods.',

                'description' =>
                'Rose Noir Eau de Parfum presents a modern interpretation of classic rose. Floral notes are softened with vanilla, musk and warm woods to create an elegant fragrance suitable for both daytime and evening wear.',

                'price_ngn' => 58000,
                'sale_price_ngn' => null,
                'price_usd' => 44.00,
                'sale_price_usd' => null,
                'cost_price_ngn' => 32000,

                'has_variants' => true,

                'quantity' => 0,
                'stock_status' => 'in_stock',

                'weight' => '0.5kg',

                'is_featured' => true,
                'is_new_arrival' => true,
                'is_best_seller' => false,
                'is_trending' => true,

                'variants' => [
                    [
                        'name' => '50ml',
                        'sku' => 'DLA-RNOIR-50',
                        'options' => [
                            'size' => '50ml',
                        ],
                        'price_ngn' => 39000,
                        'price_usd' => 30.00,
                        'stock_quantity' => 18,
                        'is_default' => false,
                    ],
                    [
                        'name' => '100ml',
                        'sku' => 'DLA-RNOIR-100',
                        'options' => [
                            'size' => '100ml',
                        ],
                        'price_ngn' => 58000,
                        'price_usd' => 44.00,
                        'stock_quantity' => 12,
                        'is_default' => true,
                    ],
                ],
            ],

            [
                'category_id' => $fragrances->id,
                'subcategory_id' => $perfumes?->id,

                'name' => 'Midnight Amber Eau de Parfum',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-MAMB-003',

                'short_description' =>
                'A warm amber fragrance with vanilla, musk and subtle spicy accords.',

                'description' =>
                'Midnight Amber is a deep evening fragrance combining amber, creamy vanilla, musk and subtle spice. Its warm character makes it ideal for special occasions and sophisticated everyday wear.',

                'price_ngn' => 62000,
                'sale_price_ngn' => 57000,
                'price_usd' => 47.00,
                'sale_price_usd' => 43.00,
                'cost_price_ngn' => 34000,

                'has_variants' => true,

                'quantity' => 0,
                'stock_status' => 'in_stock',

                'weight' => '0.5kg',

                'is_featured' => false,
                'is_new_arrival' => true,
                'is_best_seller' => true,
                'is_trending' => true,

                'variants' => [
                    [
                        'name' => '50ml',
                        'sku' => 'DLA-MAMB-50',
                        'options' => [
                            'size' => '50ml',
                        ],
                        'price_ngn' => 42000,
                        'price_usd' => 32.00,
                        'stock_quantity' => 17,
                        'is_default' => false,
                    ],
                    [
                        'name' => '100ml',
                        'sku' => 'DLA-MAMB-100',
                        'options' => [
                            'size' => '100ml',
                        ],
                        'price_ngn' => 62000,
                        'sale_price_ngn' => 57000,
                        'price_usd' => 47.00,
                        'sale_price_usd' => 43.00,
                        'stock_quantity' => 10,
                        'is_default' => true,
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | BODY CARE
            |--------------------------------------------------------------------------
            */
            [
                'category_id' => $bodyCare->id,
                'subcategory_id' => $bodyMists?->id,

                'name' => 'Vanilla Bloom Body Mist',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-VBM-004',

                'short_description' =>
                'A soft everyday body mist with creamy vanilla and delicate floral notes.',

                'description' =>
                'Vanilla Bloom Body Mist delivers a light, refreshing fragrance suitable for everyday use. Its creamy vanilla base is complemented by delicate floral notes for a soft and feminine scent.',

                'price_ngn' => 18000,
                'sale_price_ngn' => 16500,
                'price_usd' => 14.00,
                'sale_price_usd' => 12.50,
                'cost_price_ngn' => 9000,

                'has_variants' => false,

                'quantity' => 35,
                'stock_status' => 'in_stock',

                'weight' => '0.3kg',

                'is_featured' => false,
                'is_new_arrival' => true,
                'is_best_seller' => true,
                'is_trending' => true,
            ],

            [
                'category_id' => $bodyCare->id,
                'subcategory_id' => $bodyOils?->id,

                'name' => 'Golden Musk Perfume Oil',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-GMPO-005',

                'short_description' =>
                'A concentrated perfume oil featuring soft musk, amber and warm floral notes.',

                'description' =>
                'Golden Musk Perfume Oil is a concentrated fragrance oil designed for long-lasting wear. The composition combines musk, amber and warm floral accords in a smooth, elegant formulation.',

                'price_ngn' => 15000,
                'sale_price_ngn' => null,
                'price_usd' => 12.00,
                'sale_price_usd' => null,
                'cost_price_ngn' => 7500,

                'has_variants' => false,

                'quantity' => 30,
                'stock_status' => 'in_stock',

                'weight' => '0.1kg',

                'is_featured' => false,
                'is_new_arrival' => false,
                'is_best_seller' => true,
                'is_trending' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | HOME FRAGRANCE
            |--------------------------------------------------------------------------
            */
            [
                'category_id' => $homeFragrance->id,
                'subcategory_id' => $diffusers?->id,

                'name' => 'White Tea Reed Diffuser',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-WTRD-006',

                'short_description' =>
                'An elegant home diffuser with fresh white tea, citrus and soft musk notes.',

                'description' =>
                'White Tea Reed Diffuser gently fragrances living spaces with a clean and relaxing blend of white tea, citrus and soft musk. Designed for continuous home fragrance without flames or electricity.',

                'price_ngn' => 28000,
                'sale_price_ngn' => 25000,
                'price_usd' => 22.00,
                'sale_price_usd' => 19.00,
                'cost_price_ngn' => 14000,

                'has_variants' => false,

                'quantity' => 22,
                'stock_status' => 'in_stock',

                'weight' => '0.6kg',

                'is_featured' => true,
                'is_new_arrival' => false,
                'is_best_seller' => false,
                'is_trending' => true,
            ],

            [
                'category_id' => $homeFragrance->id,
                'subcategory_id' => $candles?->id,

                'name' => 'Amber Vanilla Scented Candle',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-AVSC-007',

                'short_description' =>
                'A luxurious scented candle combining warm amber with creamy vanilla.',

                'description' =>
                'Amber Vanilla Scented Candle creates a warm and inviting atmosphere through an elegant blend of amber and vanilla. Suitable for bedrooms, lounges, offices and relaxation spaces.',

                'price_ngn' => 22000,
                'sale_price_ngn' => null,
                'price_usd' => 17.00,
                'sale_price_usd' => null,
                'cost_price_ngn' => 11000,

                'has_variants' => false,

                'quantity' => 25,
                'stock_status' => 'in_stock',

                'weight' => '0.5kg',

                'is_featured' => false,
                'is_new_arrival' => true,
                'is_best_seller' => false,
                'is_trending' => false,
            ],

            /*
            |--------------------------------------------------------------------------
            | GIFT SETS
            |--------------------------------------------------------------------------
            */
            [
                'category_id' => $giftSets->id,
                'subcategory_id' => null,

                'name' => 'Signature Fragrance Gift Set',
                'brand' => 'De L\'Amoure',
                'sku' => 'DLA-GIFT-008',

                'short_description' =>
                'A premium fragrance gift set curated for birthdays, celebrations and special occasions.',

                'description' =>
                'The Signature Fragrance Gift Set combines selected De L\'Amoure fragrance products in an elegant presentation box. It is designed as a refined gift option for birthdays, anniversaries and special occasions.',

                'price_ngn' => 95000,
                'sale_price_ngn' => 89000,
                'price_usd' => 72.00,
                'sale_price_usd' => 67.00,
                'cost_price_ngn' => 52000,

                'has_variants' => false,

                'quantity' => 12,
                'stock_status' => 'in_stock',

                'weight' => '1.5kg',

                'is_featured' => true,
                'is_new_arrival' => true,
                'is_best_seller' => false,
                'is_trending' => true,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Products
        |--------------------------------------------------------------------------
        */
        foreach ($products as $index => $data) {
            $variants = $data['variants'] ?? [];

            unset($data['variants']);

            $product = Product::create([
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],

                'name' => $data['name'],
                'slug' => Str::slug($data['name']),

                'brand' => $data['brand'],
                'sku' => $data['sku'],

                'barcode' => null,

                'main_image' => null,

                'short_description' => $data['short_description'],
                'description' => $data['description'],

                /*
                |--------------------------------------------------------------------------
                | Variants
                |--------------------------------------------------------------------------
                */
                'has_variants' => $data['has_variants'],

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */
                'price_ngn' => $data['price_ngn'],
                'sale_price_ngn' => $data['sale_price_ngn'],

                'price_usd' => $data['price_usd'],
                'sale_price_usd' => $data['sale_price_usd'],

                'cost_price_ngn' => $data['cost_price_ngn'],

                'sale_starts_at' => null,
                'sale_ends_at' => null,

                /*
                |--------------------------------------------------------------------------
                | Inventory
                |--------------------------------------------------------------------------
                */
                'track_stock' => true,

                'quantity' => $data['quantity'],
                'low_stock_alert' => 5,
                'stock_status' => $data['stock_status'],

                /*
                |--------------------------------------------------------------------------
                | General Attributes
                |--------------------------------------------------------------------------
                */
                'weight' => $data['weight'] ?? null,
                'material' => $data['material'] ?? null,
                'model' => $data['model'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Merchandising
                |--------------------------------------------------------------------------
                */
                'is_featured' => $data['is_featured'],
                'is_new_arrival' => $data['is_new_arrival'],
                'is_best_seller' => $data['is_best_seller'],
                'is_trending' => $data['is_trending'],

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */
                'meta_title' => $data['name'] . ' | Shop De L\'Amoure',

                'meta_description' => $data['short_description'],

                'meta_keywords' =>
                'Shop De L\'Amoure, fragrance, perfume, beauty, scent, ' .
                    Str::lower($data['name']),

                /*
                |--------------------------------------------------------------------------
                | Publishing
                |--------------------------------------------------------------------------
                */
                'status' => true,
                'sort_order' => $index,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Product Variants
            |--------------------------------------------------------------------------
            */
            foreach ($variants as $variantIndex => $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,

                    'name' => $variant['name'],
                    'sku' => $variant['sku'],

                    'options' => $variant['options'],

                    'image' => null,

                    'price_ngn' => $variant['price_ngn'] ?? null,
                    'sale_price_ngn' => $variant['sale_price_ngn'] ?? null,

                    'price_usd' => $variant['price_usd'] ?? null,
                    'sale_price_usd' => $variant['sale_price_usd'] ?? null,

                    'track_stock' => true,

                    'stock_quantity' => $variant['stock_quantity'] ?? 0,

                    'stock_status' => ($variant['stock_quantity'] ?? 0) > 0
                        ? 'in_stock'
                        : 'out_of_stock',

                    'low_stock_threshold' => 5,

                    'is_active' => true,
                    'is_default' => $variant['is_default'] ?? false,

                    'sort_order' => $variantIndex,
                ]);
            }
        }
    }
}
