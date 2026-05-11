<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseMapping extends Model
{
    protected $table = 'warehouse_mappings';

    protected $fillable = [
        'prefix_code',
        'logistics_provider',
        'platform',
        'product_id',
        'qty_default',
    ];
}
