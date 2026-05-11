<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // ❌ MATIKAN dulu kalau sudah filter di controller
    // use HasBrandScope;

    protected $fillable = [
        'brand_id',
        'category_id',
        'uom_id',
        'sku',
        'name',
        'stock'
    ];

    // 🔥 CASTING
    protected $casts = [
        'stock' => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault();
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id')->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id');
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class, 'product_id');
    }
}
