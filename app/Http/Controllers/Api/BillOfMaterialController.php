<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillOfMaterial;

class BillOfMaterialController extends Controller
{
    // ✅ GET BOM
    public function index()
    {
        return response()->json(
            BillOfMaterial::with(['bundle', 'component'])->get()
        );
    }

    // ✅ STORE BOM
    public function store(Request $request)
    {
        foreach ($request->components as $c) {
            BillOfMaterial::create([
                'bundle_product_id' => $request->bundle_id,
                'component_product_id' => $c['product_id'],
                'qty' => $c['qty']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'BoM berhasil disimpan'
        ]);
    }
}
