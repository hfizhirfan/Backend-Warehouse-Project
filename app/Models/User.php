<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'brand_id', // ✅ cukup ini saja
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * RELATION
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * ROLE HELPERS
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuest()
    {
        return $this->role === 'guest';
    }

    /**
     * DEFAULT VALUE HANDLING
     */
    protected static function booted()
    {
        static::creating(function ($user) {

            // default role
            if (!$user->role) {
                $user->role = 'guest';
            }

            // super admin tidak punya brand
            if ($user->role === 'super_admin') {
                $user->brand_id = null;
            }
        });
    }
}
