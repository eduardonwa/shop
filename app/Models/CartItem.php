<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CartItem extends Model
{
    use HasFactory;
    
    protected $touches = ['cart'];

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: function() {
                return $this->product->price->multiply($this->quantity);
            }
        );
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // La relación hasOneThrough permite acceder a Product a través de ProductVariant.
    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,          // Modelo final (Product)
            ProductVariant::class,   // Modelo intermedio (ProductVariant)
            'id',                    // Clave foránea en el modelo intermedio (ProductVariant)
            'id',                    // Clave local en el modelo final (Product)
            'product_variant_id',    // Clave local en el modelo actual (CartItem)
            'product_id'             // Clave foránea en el modelo intermedio (ProductVariant)
        );
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
