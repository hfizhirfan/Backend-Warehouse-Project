<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\WarehouseMapping;

class OrderController extends Controller
{
    // ✅ GET ALL ORDERS + ITEMS + PRODUCT
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::query();

        if (!$user) {
    return response()->json(['message' => 'Unauthenticated'], 401);
}

if (!$user->isSuperAdmin()) {

            // filter order
            $query->whereHas('items.product', function ($q) use ($user) {
                $q->where('brand_id', $user->brand_id);
            });

            // 🔐 filter isi items juga
            $query->with(['items' => function ($q) use ($user) {
                $q->whereHas('product', function ($q2) use ($user) {
                    $q2->where('brand_id', $user->brand_id);
                });
            }, 'items.product']);

        } else {
            $query->with('items.product');
        }

        return response()->json(
            $query->latest()->get()
        );
    }

    // ✅ STORE ORDER (MANUAL INPUT)
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            // 🔢 Generate Order Number
            $orderNumber = 'ORD-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);

            // ✅ CREATE ORDER
            $order = Order::create([
                'order_number' => $orderNumber,
                'waybill_number' => $request->waybill_number,
                'platform' => $request->platform,
                'customer_name' => $request->customer_name,
                'store' => $request->store,
                'courier' => $request->courier,
                'status' => 'pending',
            ]);

            // ✅ LOOP ITEMS
            foreach ($request->items as $item) {

                // 🔥 SIMPAN KE order_items
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                // 🔥 SIMPAN KE transactions (stock keluar)
                Transaction::create([
                    'product_id' => $item['product_id'],
                    'type' => 'outbound',
                    'quantity' => $item['quantity'],
                    'qty_signed' => -$item['quantity'],

                    'reference_type' => 'order',
                    'reference_number' => $order->order_number,

                    'order_id' => $order->id,

                    'platform' => $order->platform,
                    'store' => $order->store,
                    'courier' => $order->courier,
                    'customer' => $order->customer_name,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat'
        ]);
    }

    // ✅ GET ORDER BY WAYBILL
    public function byWaybill($waybill)
    {
        $order = Order::with('items.product')
            ->whereRaw('UPPER(waybill_number) = ?', [strtoupper($waybill)])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json($order);
    }

    // ✅ SCAN → AUTO CREATE ORDER (JIKA BELUM ADA)
    public function scanOrder(Request $request)
    {
        $request->validate([
            'waybill_number' => 'required|string'
        ]);

        try {

            $waybill = strtoupper($request->waybill_number);

            // ✅ 1. Cek apakah order sudah ada
            $existingOrder = Order::with('items.product')
                ->where('waybill_number', $waybill)
                ->first();

            if ($existingOrder) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order sudah ada',
                    'order' => $existingOrder
                ]);
            }

            // ✅ 2. Ambil prefix
            $prefix = substr($waybill, 0, 2);

            // ✅ 3. Ambil salah satu mapping untuk info header (platform, courier)
            $baseMapping = WarehouseMapping::where('prefix_code', $prefix)
                ->where('platform', 'Tiktok') // nanti bisa dibuat dinamis
                ->first();

            if (!$baseMapping) {
                return response()->json([
                    'success' => false,
                    'message' => "Mapping tidak ditemukan untuk prefix {$prefix}"
                ], 404);
            }

            // ✅ 4. Generate Order Number
            $orderNumber = 'ORD-' . str_pad((Order::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);

            // ✅ 5. Create Order
            $order = Order::create([
                'order_number'   => $orderNumber,
                'waybill_number' => $waybill,
                'customer_name'  => '-',
                'platform'       => $baseMapping->platform,
                'store'          => $baseMapping->platform,
                'courier'        => $baseMapping->logistics_provider,
                'status'         => 'pending',
            ]);

            // ✅ 6. Ambil SEMUA mapping (MULTI PRODUCT)
            $mappings = WarehouseMapping::where('prefix_code', $prefix)
                ->where('platform', $baseMapping->platform)
                ->get();

            if ($mappings->isEmpty()) {
                throw new \Exception('Mapping tidak ditemukan');
            }

            // ✅ 7. Loop insert banyak item
            foreach ($mappings as $map) {

                if (!$map->product_id) {
                    continue; // skip kalau kosong
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $map->product_id,
                    'quantity'   => $map->qty_default ?? 1,
                ]);
            }

            // ✅ 8. Load relasi
            $order->load('items.product');

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat dari scan (multi mapping)',
                'order'   => $order
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal scan order',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function extractPrefix($waybill)
    {
        return strtoupper(substr($waybill, 0, 2));
    }
}
