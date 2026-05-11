<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class StockService
{
    public function getStock($productId)
    {
        return Transaction::where('product_id', $productId)
            ->sum('qty_signed');
    }

    public function checkStock($productId, $qty)
    {
        $stock = $this->getStock($productId);

        if ($stock < $qty) {
            throw new \Exception("Stock tidak cukup");
        }
    }

    public function getStockWithLock($productId)
    {
        $rows = DB::table('transactions')
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->get(); // ambil data dulu

        return $rows->sum('qty_signed'); // baru dihitung di PHP
    }
}
