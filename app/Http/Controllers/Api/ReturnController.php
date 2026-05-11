<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ReturnItem::with('product');

        if (!$user) {
    return response()->json(['message' => 'Unauthenticated'], 401);
}

if (!$user->isSuperAdmin()) {
            $query->whereHas('product', function ($q) use ($user) {
                $q->where('brand_id', $user->brand_id);
            });
        }

        return $query->latest()->get();
    }

    public function today()
    {
        return ReturnItem::with('product')
            ->whereDate('created_at', now())
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'waybill' => 'required',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'condition' => 'required|in:good,defect,lost',
        ]);

        $product = Product::find($request->product_id);

        DB::beginTransaction();

        try {

            $return = ReturnItem::create([
                'waybill' => $request->waybill,
                'ekspedisi' => $request->ekspedisi,
                'platform' => $request->platform,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'condition' => $request->condition,
                'status' => $request->condition === 'good' ? 'restocked' : 'damaged'
            ]);

            // ✅ tambah stok kalau barang bagus
            if ($request->condition === 'good') {
                $product->stock += $return->qty;
                $product->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Return berhasil'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal proses return',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
