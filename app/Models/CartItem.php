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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accesor para obtener el producto resolviendo la relación
    public function getResolvedProductAttribute()
    {
        return $this->product_variant_id 
            ? $this->variant->product 
            : $this->product;
    }

    // Accesor para la descripción
    public function getDescriptionAttribute()
    {
        if ($this->product_variant_id && $this->variant) {
            return $this->variant->attributes->map(function($attr) {
                return "{$attr->attribute->key}: {$attr->value}";
            })->implode(', ');
        }
        return 'Producto estándar';
    }

/*     // La relación hasOneThrough permite acceder a Product a través de ProductVariant.
    public function product(): HasOneThrough|BelongsTo
    {
        // si un producto viene con una variante accedemos a estas relaciones
        if ($this->product_variant_id) {
            return $this->hasOneThrough(
                Product::class,          // Modelo final (Product)
                ProductVariant::class,   // Modelo intermedio (ProductVariant)
                'id',                    // Clave foránea en el modelo intermedio (ProductVariant)
                'id',                    // Clave local en el modelo final (Product)
                'product_variant_id',    // Clave local en el modelo actual (CartItem)
                'product_id'             // Clave foránea en el modelo intermedio (ProductVariant)
            );
        }
        // regresar si el producto no tiene variantes
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    } */
}
