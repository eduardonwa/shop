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

    public function scopeValid($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', $now);
            });
    }

    public function isValid()
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->starts_at && $this->starts_at->gt($now)) return false;
        if ($this->expires_at && $this->expires_at->lt($now)) return false;
        
        return true;
    }

    public function applyDiscount($price, $context = null)
    {
        if (!$this->valid()) return $price;

        // manejo según tipo de descuento y alcance
        switch ($this->discount_type) {
            case 'percentage':
                return $price * (1 - $this->discount_value / 100);
            
            case 'fixed':
                if ($this->scope === 'product') {
                    return max(0, $price - $this->discount_value);
                }

                if ($context && $this->scope === 'cart') {
                    $proportionalDiscount = ($price / $context) * $this->discount_value;
                    return max(0, $price - $proportionalDiscount);
                }

                return max(0, $price - $this->discount_value);

                default:
                    return $price;
        }
    }
}
