<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'waybill',
        'ekspedisi',
        'platform',
        'product_id',
        'qty',
        'condition',
        'status'
    ];

    // relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
