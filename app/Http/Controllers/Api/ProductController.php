<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = Product::with(['brand', 'category', 'uom']);

        if (!$user->isSuperAdmin()) {
            if (!$user->brand_id) {
                return response()->json([
                    'message' => 'User tidak punya brand_id'
                ], 400);
            }

            $query->where('brand_id', $user->brand_id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->isGuest()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        // Paksa brand sesuai user jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $request->merge([
                'brand_id' => $user->brand_id
            ]);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $product = Product::with(['brand', 'category', 'uom'])->findOrFail($id);

        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->isGuest()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product = Product::findOrFail($id);

        // Cek akses brand
        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Cegah ubah brand_id
        $data = $request->except(['brand_id']);
        $product->update($data);

        return response()->json($product);
    }

    public function dashboard(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->isSuperAdmin() && !$user->brand_id) {
            return response()->json([
                'message' => 'User tidak punya brand_id'
            ], 400);
        }

        $days = $request->get('days', 7);
        $from = now()->subDays($days);

        // Filter product
        $productQuery = Product::with('brand');

        if (!$user->isSuperAdmin()) {
            $productQuery->where('brand_id', $user->brand_id);
        }

        $products = $productQuery->get();

        // Filter transaction
        $transactionQuery = Transaction::where('created_at', '>=', $from);

        if (!$user->isSuperAdmin()) {
            $transactionQuery->whereHas('product', function ($q) use ($user) {
                $q->where('brand_id', $user->brand_id);
            });
        }

        $inbound = (clone $transactionQuery)
            ->where('type', 'inbound')
            ->sum('qty_signed');

        $outbound = (clone $transactionQuery)
            ->where('type', 'outbound')
            ->sum('qty_signed');

        return response()->json([
            'total_stock' => $products->sum('stock'),
            'low_stock' => $products->where('stock', '<', 50)->count(),
            'total_inbound' => $inbound,
            'total_outbound' => abs($outbound),
            'products' => $products
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->isGuest()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product = Product::findOrFail($id);

        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
