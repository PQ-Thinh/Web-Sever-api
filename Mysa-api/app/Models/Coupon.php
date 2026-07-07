<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'discount_amount', 'discount_type', 'usage_limit', 'used_count', 'valid_until'];

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}
