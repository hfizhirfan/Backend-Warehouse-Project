<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * GET: list semua transaksi
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = Transaction::with(['product.brand', 'order']);

        // filter brand kalau bukan super admin
        if (!$user->isSuperAdmin()) {

            if (!$user->brand_id) {
                return response()->json([
                    'message' => 'User tidak punya brand_id'
                ], 400);
            }

            $query->whereHas('product', function ($q) use ($user) {
                $q->where('brand_id', $user->brand_id);
            });
        }

        $data = $query->latest()->paginate(20);

        return response()->json($data);
    }

    /**
     * INBOUND (Barang Masuk)
     */
    public function inbound(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'transaction_date' => 'nullable|date',
        ]);

        $product = \App\Models\Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $product = \App\Models\Product::lockForUpdate()->find($request->product_id);
                $product->increment('stock', $request->qty);
                $product->refresh();

                Transaction::create([
                    'product_id' => $request->product_id,
                    'type' => 'inbound',
                    'operation' => 'supplier_inbound',
                    'transaction_date' => $request->transaction_date ?? now(),
                    'quantity' => $request->qty,
                    'qty_signed' => $request->qty,
                    'end_qty' => $product->stock,
                    'is_qt_product' => true,
                    'identifier' => $product->sku . '-' . time(),
                    'reference_type' => 'invoice',
                    'reference_number' => $request->reference_number,
                    'supplier' => $request->supplier,
                    'remark' => $request->remark,
                    'created_by' => $user->name ?? 'system',
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Inbound berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal inbound', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * OUTBOUND (Barang Keluar) - AMAN DENGAN LOCK
     */
    public function outbound(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // 🔥 VALIDASI REQUEST
        $request->validate([
            'waybill_number' => 'required',
            'customer_name' => 'nullable|string',
            'platform' => 'nullable|string',
            'store' => 'nullable|string',
            'courier' => 'nullable|string',
            'transaction_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // 🔥 WAJIB: VALIDASI brand_id
        if (!$user->isSuperAdmin() && !$user->brand_id) {
            return response()->json([
                'message' => 'User tidak punya brand_id'
            ], 400);
        }

        // 🔥 PRE-CHECK AKSES PRODUCT
        if (!$user->isSuperAdmin()) {
            $productIds = collect($request->items)->pluck('product_id')->unique();

            $invalidProducts = \App\Models\Product::whereIn('id', $productIds)
                ->where('brand_id', '!=', $user->brand_id)
                ->count();

            if ($invalidProducts > 0) {
                return response()->json([
                    'message' => 'Forbidden access to one or more products'
                ], 403);
            }
        }

        try {
            DB::transaction(function () use ($request, $user) {

                // 🔥 CREATE / FIND ORDER
                $order = \App\Models\Order::firstOrCreate(
                    ['waybill_number' => $request->waybill_number],
                    [
                        'order_number' => 'ORD-' . str_pad(\App\Models\Order::count() + 1, 6, '0', STR_PAD_LEFT),
                        'status' => 'shipped'
                    ]
                );

                // 🔥 UPDATE ORDER INFO
                $order->update([
                    'platform' => $request->platform,
                    'store' => $request->store,
                    'courier' => $request->courier,
                    'customer_name' => $request->customer_name ?? $order->customer_name,
                ]);

                // 🔥 LOOP ITEMS
                foreach ($request->items as $item) {

                    $product = \App\Models\Product::lockForUpdate()->find($item['product_id']);

                    // ❗ SAFE CHECK
                    if (!$product) {
                        throw new \Exception("Product tidak ditemukan ID {$item['product_id']}");
                    }

                    // ❗ CEK STOCK
                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stock tidak cukup untuk SKU ID {$item['product_id']}");
                    }

                    // 🔻 KURANGI STOCK
                    $product->decrement('stock', $item['qty']);
                    $product->refresh();

                    // 🔥 ORDER ITEM
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['qty'],
                    ]);

                    // 🔥 TRANSACTION LOG
                    Transaction::create([
                        'product_id' => $item['product_id'],
                        'type' => 'outbound',
                        'operation' => 'order_fulfillment',
                        'transaction_date' => $request->transaction_date ?? now(),
                        'quantity' => $item['qty'],
                        'qty_signed' => -$item['qty'],
                        'end_qty' => $product->stock,
                        'reference_type' => 'waybill',
                        'reference_number' => $request->waybill_number,
                        'order_id' => $order->id,
                        'platform' => $request->platform,
                        'store' => $request->store,
                        'courier' => $request->courier,
                        'customer' => $request->customer_name,
                        'remark' => $request->remark,
                        'created_by' => $user->name ?? 'system',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Outbound berhasil (multi item)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function return(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'condition' => 'required|in:good,bad',
        ]);

        $product = \App\Models\Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $product = \App\Models\Product::lockForUpdate()->find($request->product_id);

                $qtySigned = $request->condition === 'good' ? $request->qty : 0;

                if ($request->condition === 'good') {
                    $product->increment('stock', $request->qty);
                }

                Transaction::create([
                    'product_id' => $request->product_id,
                    'type' => 'return',
                    'quantity' => $request->qty,
                    'qty_signed' => $qtySigned,
                    'return_condition' => $request->condition,
                    'reference_type' => 'return',
                    'reference_number' => $request->reference_number,
                    'remark' => $request->remark,
                    'created_by' => $user->name ?? 'system',
                ]);
            });

            return response()->json(['success' => true, 'message' => 'Return berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal return', 'error' => $e->getMessage()], 500);
        }
    }


    public function adjustment(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer',
            'reference_number' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        $product = \App\Models\Product::find($request->product_id);

        // 🔥 WAJIB: product harus ada
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }

        // 🔥 VALIDASI brand_id
        if (!$user->isSuperAdmin() && !$user->brand_id) {
            return response()->json([
                'message' => 'User tidak punya brand_id'
            ], 400);
        }

        // 🔐 CEK AKSES BRAND
        if (!$user->isSuperAdmin() && $product->brand_id !== $user->brand_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            DB::transaction(function () use ($request, $user) {

                $product = \App\Models\Product::lockForUpdate()->find($request->product_id);

                if (!$product) {
                    throw new \Exception("Product tidak ditemukan");
                }

                // 🔄 Update stock (bisa + atau -)
                $product->increment('stock', $request->qty);
                $product->refresh();

                Transaction::create([
                    'product_id' => $product->id,
                    'type' => 'adjustment',
                    'quantity' => abs($request->qty),
                    'qty_signed' => $request->qty,
                    'end_qty' => $product->stock,
                    'reference_type' => 'opname',
                    'reference_number' => $request->reference_number,
                    'remark' => $request->remark ?? 'Stock Opname',
                    'created_by' => $user->name ?? 'system',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Adjustment berhasil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET STOCK PER PRODUCT
     */
    public function stock($productId, StockService $stockService)
    {
        $stock = $stockService->getStock($productId);
        return response()->json(['product_id' => $productId, 'stock' => $stock]);
    }

    public function scanOutbound(Request $request, StockService $stockService)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'waybill_number' => 'required'
        ]);

        // 🔥 VALIDASI brand_id
        if (!$user->isSuperAdmin() && !$user->brand_id) {
            return response()->json([
                'message' => 'User tidak punya brand_id'
            ], 400);
        }

        try {
            $order = Order::with('items.product')
                ->where('waybill_number', $request->waybill_number)
                ->firstOrFail();

            // ❗ Kalau sudah shipped
            if ($order->status === 'shipped') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order sudah dikirim',
                    'order' => $order
                ]);
            }

            // 🔐 CEK AKSES BRAND
            if (!$user->isSuperAdmin()) {
                foreach ($order->items as $item) {
                    if (!$item->product || $item->product->brand_id !== $user->brand_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Forbidden access to one or more products in this order',
                            'order' => null
                        ], 403);
                    }
                }
            }

            DB::transaction(function () use ($order, $user) {

                foreach ($order->items as $item) {

                    $product = \App\Models\Product::lockForUpdate()->find($item->product_id);

                    if (!$product) {
                        throw new \Exception("Product tidak ditemukan ID {$item->product_id}");
                    }

                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Stock tidak cukup untuk SKU ID {$item->product_id}");
                    }

                    // 🔻 Kurangi stock
                    $product->decrement('stock', $item->quantity);
                    $product->refresh();

                    Transaction::create([
                        'product_id' => $item->product_id,
                        'type' => 'outbound',
                        'operation' => 'order_fulfillment',
                        'transaction_date' => now(),
                        'quantity' => $item->quantity,
                        'qty_signed' => -$item->quantity,
                        'end_qty' => $product->stock,
                        'is_qt_product' => true,
                        'identifier' => $product->sku . '-' . time(),
                        'reference_type' => 'waybill',
                        'reference_number' => $order->waybill_number,
                        'order_id' => $order->id,
                        'platform' => $order->platform,
                        'store' => $order->store,
                        'courier' => $order->courier,
                        'customer' => $order->customer_name,
                        'remark' => 'Scan Outbound',
                        'created_by' => $user->name ?? 'system',
                    ]);
                }

                // 🔥 Update status order
                $order->update(['status' => 'shipped']);
            });

            $order->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Outbound berhasil (scan resi)',
                'order' => $order->load('items.product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'order' => null
            ], 400);
        }
    }

    public function reportOutbound(Request $request)
    {
         $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = Transaction::with('product')->where('type', 'outbound')->latest();

        if (!$user->isSuperAdmin()) {

            if (!$user->brand_id) {
                return response()->json([
                    'message' => 'User tidak punya brand_id'
                ], 400);
            }

            $query->whereHas('product', function ($q) use ($user) {
                $q->where('brand_id', $user->brand_id);
            });
        }

        $data = $query->get()->map(function ($t) {
            return [
                'date' => $t->transaction_date,
                'sku' => $t->product->sku ?? null,
                'product_name' => $t->product->name ?? null,
                'quantity' => $t->qty_signed,
                'platform' => $t->platform,
                'store' => $t->store,
                'courier' => $t->courier,
            ];
        });

        return response()->json($data);
    }
}
