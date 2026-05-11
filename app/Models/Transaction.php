<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'qty_signed',
        'reference_type',
        'reference_number',

        'supplier',
        'customer',

        'platform',
        'store',
        'courier',
        'operation',

        'order_id',
        'transaction_date',
        'return_condition',
        'remark',
        'created_by',

        'end_qty',
        'is_qt_product',
        'identifier',
    ];

    // 🔥 CASTING (WAJIB BIAR STABIL)
    protected $casts = [
        'quantity' => 'integer',
        'qty_signed' => 'integer',
        'end_qty' => 'integer',
        'is_qt_product' => 'boolean',
        'transaction_date' => 'datetime',
    ];

    // 🔗 RELATION (SAFE)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id')->withDefault();
    }
}
