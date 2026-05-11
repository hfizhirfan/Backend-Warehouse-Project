<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarehouseMapping;
use Illuminate\Http\Request;

class WarehouseMappingController extends Controller
{
    public function index()
    {
        return WarehouseMapping::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix_code' => 'required|string|max:10',
            'logistics_provider' => 'required|string|max:50',
            'platform' => 'required|string|max:50',
            'product_id' => 'nullable|exists:products,id',
            'qty_default' => 'nullable|integer|min:1',
        ]);

        return WarehouseMapping::create($validated);
    }

    public function update(Request $request, $id)
    {
        $data = WarehouseMapping::findOrFail($id);

        $validated = $request->validate([
            'prefix_code' => 'required|string|max:10',
            'logistics_provider' => 'required|string|max:50',
            'platform' => 'required|string|max:50',
            'product_id' => 'nullable|exists:products,id',
            'qty_default' => 'nullable|integer|min:1',
        ]);

        $data->update($validated);

        return $data;
    }

    public function destroy($id)
    {
        WarehouseMapping::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
