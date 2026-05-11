<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\Product;

class StockOpnameController extends Controller
{
    /**
     * 📦 GET /opname
     * Ambil data produk + stok + brand
     */
    public function index()
    {
        try {
            $products = Product::with('brand')->get();

            $data = $products->map(function ($p) {
                return [
                    'sku'       => $p->sku,
                    'name'      => $p->name,
                    'stock'     => (int) $p->stock,
                    'brand'     => $p->brand->name ?? null,
                    'brand_id'  => $p->brand_id, // 🔥 INI YANG PALING PENTING
                ];
            });

            return response()->json($data);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line'  => $e->getLine()
            ], 500);
        }
    }

    /**
     * 💾 POST /opname
     * Simpan hasil opname + update stok
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|exists:products,sku',
            'items.*.system_stock' => 'required|integer|min:0',
            'items.*.physical_stock' => 'required|integer|min:0',
            'items.*.selisih' => 'required|integer',
            'items.*.remark' => 'nullable|string'
        ]);

        try {

            foreach ($request->items as $item) {

                $difference = $item['physical_stock'] - $item['system_stock'];

                // simpan opname
                StockOpname::create([
                    'sku'            => $item['sku'],
                    'system_stock'   => $item['system_stock'],
                    'physical_stock' => $item['physical_stock'],
                    'difference'     => $difference,
                    'date'           => $request->date,
                    'remark'         => $item['remark'] ?? null,
                ]);

                // update stok produk
                $product = Product::where('sku', $item['sku'])->first();

                if ($product) {
                    $product->stock = $item['physical_stock'];
                    $product->save();
                }
            }

            return response()->json([
                'message' => 'Stock opname berhasil disimpan'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line'  => $e->getLine()
            ], 500);
        }
    }
}
