<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'waybill_number',
        'platform',
        'store',
        'courier',
        'customer_name',
        'status'
    ];

    // 🔗 RELATION
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
