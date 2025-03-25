<?php

namespace App\Models;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;

class Couponable extends Model
{
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function couponable()
    {
        return $this->morphTo();
    }
}
