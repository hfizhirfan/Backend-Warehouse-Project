<?php

namespace App\Traits;

trait HasBrandScope
{
    public function scopeByUser($query, $user)
    {
        if (!$user) {
    return response()->json(['message' => 'Unauthenticated'], 401);
}

if (!$user->isSuperAdmin()) {
            return $query->where('brand_id', $user->brand_id);
        }

        return $query;
    }
}
