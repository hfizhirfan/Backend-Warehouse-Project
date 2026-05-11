<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    protected $table = 'bill_of_materials';

    protected $fillable = [
        'bundle_product_id',
        'component_product_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    protected $with = ['bundle', 'component'];

    public function bundle()
    {
        return $this->belongsTo(Product::class, 'bundle_product_id');
    }

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
