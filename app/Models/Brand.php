<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'contact_person'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
