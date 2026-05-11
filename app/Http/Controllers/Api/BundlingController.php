<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillOfMaterial;
use App\Models\Product;

class BundlingController extends Controller
{
    public function process(Request $request)
    {
        $productId = $request->product_id;
        $qty = $request->qty;

        // ambil BOM
        $boms = BillOfMaterial::where('bundle_product_id', $productId)->get();

        if ($boms->isEmpty()) {
            return response()->json([
                'message' => 'BoM tidak ditemukan'
            ], 400);
        }

        // LOOP potong stok komponen
        foreach ($boms as $bom) {
            $component = Product::find($bom->component_product_id);

            $totalPotong = $bom->qty * $qty;

            if ($component->stock < $totalPotong) {
                return response()->json([
                    'message' => "Stok tidak cukup untuk {$component->sku}"
                ], 400);
            }

            $component->stock -= $totalPotong;
            $component->save();
        }

        // tambah stok bundle
        $bundle = Product::find($productId);
        $bundle->stock += $qty;
        $bundle->save();

        return response()->json([
            'message' => 'Bundling berhasil'
        ]);
    }
}
