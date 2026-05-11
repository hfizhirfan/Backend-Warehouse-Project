<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([

            // ✅ BRAND 3 - Glow Beauty Lab
            [
                'brand_id' => 3,
                'category_id' => 1,
                'uom_id' => 1,
                'sku' => 'GBL001',
                'name' => 'Glow Serum Vitamin C',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ✅ BRAND 4 - FreshCare Indonesia
            [
                'brand_id' => 4,
                'category_id' => 3,
                'uom_id' => 1,
                'sku' => 'FC001',
                'name' => 'Fresh Hair Tonic',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ✅ BRAND 5 - Natural Skin Co
            [
                'brand_id' => 5,
                'category_id' => 4,
                'uom_id' => 1,
                'sku' => 'NS001',
                'name' => 'Natural Body Lotion',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ✅ BRAND 6 - Aura Cosmetics
            [
                'brand_id' => 6,
                'category_id' => 5,
                'uom_id' => 1,
                'sku' => 'AC001',
                'name' => 'Aura Lip Cream',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ✅ BRAND 7 - Dermacare Plus
            [
                'brand_id' => 7,
                'category_id' => 6,
                'uom_id' => 1,
                'sku' => 'DP001',
                'name' => 'Dermacare Perfume',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ✅ CATEGORY 7 - Tools & Accessories
            [
                'brand_id' => 2,
                'category_id' => 7,
                'uom_id' => 2,
                'sku' => 'ACC001',
                'name' => 'Spatula Skincare',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
