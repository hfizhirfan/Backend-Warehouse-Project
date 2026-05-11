<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // BRANDS
        // =========================
        DB::table('brands')->insert([
            ['name' => 'CA SKIN GLOW', 'contact_person' => null],
            ['name' => 'Packaging Packing', 'contact_person' => null],
        ]);

        // =========================
        // CATEGORIES
        // =========================
        DB::table('categories')->insert([
            ['name' => 'Skincare'],
            ['name' => 'Packing'],
        ]);

        // =========================
        // UOM
        // =========================
        DB::table('uoms')->insert([
            ['name' => 'Pieces'],
            ['name' => 'Ikat'],
        ]);

        // =========================
        // PRODUCTS
        // =========================
        DB::table('products')->insert([
            [
                'brand_id' => 1,
                'category_id' => 1,
                'uom_id' => 1,
                'sku' => 'CAM001',
                'name' => 'Cooper Moisturizer 10 gr',
                'stock' => 500,
            ],
            [
                'brand_id' => 1,
                'category_id' => 1,
                'uom_id' => 1,
                'sku' => 'CAS001',
                'name' => 'Cooper Peptide Luxe Serum',
                'stock' => 800,
            ],
            [
                'brand_id' => 1,
                'category_id' => 1,
                'uom_id' => 1,
                'sku' => 'CASM001',
                'name' => 'White Truffle Serum Mist 60 mL',
                'stock' => 500,
            ],
            [
                'brand_id' => 2,
                'category_id' => 2,
                'uom_id' => 2,
                'sku' => 'POLY01',
                'name' => 'Polymailer 17 x 30',
                'stock' => 10,
            ],
            [
                'brand_id' => 2,
                'category_id' => 2,
                'uom_id' => 2,
                'sku' => 'POLY02',
                'name' => 'Polymailer 30 x 30',
                'stock' => 10,
            ],
        ]);

        // =========================
        // WAREHOUSES
        // =========================
        DB::table('warehouses')->insert([
            ['name' => 'Warehouse Fulfillment'],
        ]);

        // =========================
        // WAREHOUSE MAPPINGS
        // =========================
        DB::table('warehouse_mappings')->insert([
            ['prefix_code' => 'JX', 'logistics_provider' => 'JNT', 'platform' => 'Tiktok'],
            ['prefix_code' => 'JP', 'logistics_provider' => 'JNT', 'platform' => 'Shopee'],
            ['prefix_code' => 'JO', 'logistics_provider' => 'JNT', 'platform' => 'Mengantar'],
            ['prefix_code' => 'TK', 'logistics_provider' => 'JNT', 'platform' => 'Tokopedia'],
            ['prefix_code' => 'TJ', 'logistics_provider' => 'JNT', 'platform' => 'Tokopedia'],
            ['prefix_code' => 'SP', 'logistics_provider' => 'SPX', 'platform' => 'Shopee'],
            ['prefix_code' => 'ID', 'logistics_provider' => 'ID Express', 'platform' => 'Mengantar'],
            ['prefix_code' => 'LX', 'logistics_provider' => 'LEX ID', 'platform' => 'Lazada'],
            ['prefix_code' => 'JN', 'logistics_provider' => 'LEX ID', 'platform' => 'Lazada'],
            ['prefix_code' => 'NL', 'logistics_provider' => 'LEX ID', 'platform' => 'Lazada'],
            ['prefix_code' => 'NX', 'logistics_provider' => 'Ninja Express', 'platform' => 'Mengantar'],
            ['prefix_code' => 'AK', 'logistics_provider' => 'SAP', 'platform' => 'Mengantar'],
            ['prefix_code' => 'TG', 'logistics_provider' => 'JNE', 'platform' => 'Tiktok'],
            ['prefix_code' => 'JZ', 'logistics_provider' => 'LEX ID', 'platform' => 'Lazada'],
            ['prefix_code' => 'NJ', 'logistics_provider' => 'Ninja Express', 'platform' => 'Tiktok'],
            ['prefix_code' => 'JJ', 'logistics_provider' => 'JNT', 'platform' => 'Blibli'],
            ['prefix_code' => 'MG', 'logistics_provider' => 'SAP', 'platform' => 'Mengantar'],
            ['prefix_code' => 'BLI', 'logistics_provider' => 'Instant', 'platform' => 'Blibli'],
            ['prefix_code' => 'ED', 'logistics_provider' => 'JNE', 'platform' => 'Endorsement'],
            ['prefix_code' => 'AF', 'logistics_provider' => 'JNT', 'platform' => 'Affiliate'],
            ['prefix_code' => 'CT', 'logistics_provider' => 'JNT', 'platform' => 'Complaint'],
            ['prefix_code' => '001', 'logistics_provider' => 'Sicepat', 'platform' => 'Mengantar'],
            ['prefix_code' => '03', 'logistics_provider' => 'JNE', 'platform' => 'Mengantar'],
            ['prefix_code' => 'TS', 'logistics_provider' => 'Anteraja', 'platform' => 'Tiktok'],
        ]);
    }
}
