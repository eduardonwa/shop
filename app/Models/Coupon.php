<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Couponable;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function couponables()
    {
        return $this->hasMany(Couponable::class);
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'couponable');
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->expires_at) return null;
        
        return now()
            ->setTimezone('America/Hermosillo')
            ->diffAsCarbonInterval($this->expires_at->setTimezone('America/Hermosillo'))
            ->forHumans(['short' => true]); // Ej: "1d 5h"
    }

    /* public function collections()
    {
        return $this->morphedByMany(ProductCollection::class, 'couponable');
    } */

    public function isValid()
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->starts_at && $this->starts_at->gt($now)) return false;
        if ($this->expires_at && $this->expires_at->gt($now)) return false;
        
        return true;
    }

    public function applyDiscount($price)
    {
        if (!$this->isValid()) return $price;

        return $this->discount_type === 'percentage'
            ? $price * (1 - $this->discount_value / 100)
            : max(0, $price - $this->discount_value); 
    }
}
