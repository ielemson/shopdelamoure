<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Main Categories
        |--------------------------------------------------------------------------
        */

        $mainCategories = [

            [
                'name' => 'Fragrances',
                'slug' => 'fragrances',
                'description' =>
                'Discover signature perfumes, perfume oils and distinctive fragrances for every mood and occasion.',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Body Care',
                'slug' => 'body-care',
                'description' =>
                'Beautifully scented body care products designed for everyday freshness, nourishment and indulgence.',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'Home Fragrance',
                'slug' => 'home-fragrance',
                'description' =>
                'Create an inviting atmosphere with elegant candles, diffusers, room fragrances and home scent products.',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 3,
            ],

            [
                'name' => 'Gift Sets',
                'slug' => 'gift-sets',
                'description' =>
                'Curated fragrance and beauty gift collections for birthdays, celebrations and special occasions.',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($mainCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'parent_id' => null,
                    'name' => $category['name'],
                    'image' => null,
                    'description' => $category['description'],
                    'is_featured' => $category['is_featured'],
                    'status' => $category['status'],
                    'sort_order' => $category['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Parent Categories
        |--------------------------------------------------------------------------
        */

        $fragrances = DB::table('categories')
            ->where('slug', 'fragrances')
            ->first();

        $bodyCare = DB::table('categories')
            ->where('slug', 'body-care')
            ->first();

        $homeFragrance = DB::table('categories')
            ->where('slug', 'home-fragrance')
            ->first();

        $giftSets = DB::table('categories')
            ->where('slug', 'gift-sets')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Fragrance Subcategories
        |--------------------------------------------------------------------------
        */

        $fragranceCategories = [

            [
                'name' => 'Perfumes',
                'slug' => 'perfumes',
                'description' =>
                'Eau de parfum and signature fragrance collections for lasting personal scent.',
                'is_featured' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Perfume Oils',
                'slug' => 'perfume-oils',
                'description' =>
                'Concentrated fragrance oils offering rich and long-lasting scent experiences.',
                'is_featured' => false,
                'sort_order' => 2,
            ],

            [
                'name' => 'Unisex Fragrances',
                'slug' => 'unisex-fragrances',
                'description' =>
                'Versatile fragrances created to be enjoyed regardless of gender.',
                'is_featured' => false,
                'sort_order' => 3,
            ],

            [
                'name' => 'Travel & Mini Fragrances',
                'slug' => 'travel-mini-fragrances',
                'description' =>
                'Compact fragrances and travel-friendly sizes for scent on the go.',
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($fragranceCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'parent_id' => $fragrances->id,
                    'name' => $category['name'],
                    'image' => null,
                    'description' => $category['description'],
                    'is_featured' => $category['is_featured'],
                    'status' => true,
                    'sort_order' => $category['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Body Care Subcategories
        |--------------------------------------------------------------------------
        */

        $bodyCareCategories = [

            [
                'name' => 'Body Mists',
                'slug' => 'body-mists',
                'description' =>
                'Light and refreshing scented body mists for everyday wear.',
                'is_featured' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Body Oils',
                'slug' => 'body-oils',
                'description' =>
                'Fragrant body oils designed to nourish the skin while leaving a lasting scent.',
                'is_featured' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'Body Lotions',
                'slug' => 'body-lotions',
                'description' =>
                'Moisturising body lotions with elegant fragrance profiles.',
                'is_featured' => false,
                'sort_order' => 3,
            ],

            [
                'name' => 'Bath & Shower',
                'slug' => 'bath-shower',
                'description' =>
                'Cleansing and refreshing bath and shower essentials with luxurious scents.',
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($bodyCareCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'parent_id' => $bodyCare->id,
                    'name' => $category['name'],
                    'image' => null,
                    'description' => $category['description'],
                    'is_featured' => $category['is_featured'],
                    'status' => true,
                    'sort_order' => $category['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Home Fragrance Subcategories
        |--------------------------------------------------------------------------
        */

        $homeFragranceCategories = [

            [
                'name' => 'Reed Diffusers',
                'slug' => 'reed-diffusers',
                'description' =>
                'Elegant reed diffusers providing continuous fragrance throughout your space.',
                'is_featured' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Scented Candles',
                'slug' => 'scented-candles',
                'description' =>
                'Atmospheric scented candles designed to enhance relaxation and ambience.',
                'is_featured' => true,
                'sort_order' => 2,
            ],

            [
                'name' => 'Room Sprays',
                'slug' => 'room-sprays',
                'description' =>
                'Instant home fragrance sprays for refreshing bedrooms, lounges and workspaces.',
                'is_featured' => false,
                'sort_order' => 3,
            ],

            [
                'name' => 'Car Fragrances',
                'slug' => 'car-fragrances',
                'description' =>
                'Premium fragrance solutions designed to keep vehicle interiors fresh and inviting.',
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($homeFragranceCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'parent_id' => $homeFragrance->id,
                    'name' => $category['name'],
                    'image' => null,
                    'description' => $category['description'],
                    'is_featured' => $category['is_featured'],
                    'status' => true,
                    'sort_order' => $category['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Gift Set Subcategories
        |--------------------------------------------------------------------------
        */

        $giftCategories = [

            [
                'name' => 'Fragrance Gift Sets',
                'slug' => 'fragrance-gift-sets',
                'description' =>
                'Curated perfume and fragrance collections presented for gifting.',
                'is_featured' => true,
                'sort_order' => 1,
            ],

            [
                'name' => 'Discovery Sets',
                'slug' => 'discovery-sets',
                'description' =>
                'A selection of fragrances designed for customers who want to explore multiple scents.',
                'is_featured' => false,
                'sort_order' => 2,
            ],

            [
                'name' => 'Luxury Gift Boxes',
                'slug' => 'luxury-gift-boxes',
                'description' =>
                'Premium gift boxes combining selected fragrance, body care and home scent products.',
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($giftCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'parent_id' => $giftSets->id,
                    'name' => $category['name'],
                    'image' => null,
                    'description' => $category['description'],
                    'is_featured' => $category['is_featured'],
                    'status' => true,
                    'sort_order' => $category['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
