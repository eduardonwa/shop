<?php

namespace App\Models;

use App\Models\Product;
use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $appends = ['product_name'];

    public function getProductNameAttribute()
    {
        return $this->product->name ?? 'Producto eliminado';
    }

    public $casts = [
        'price' => MoneyCast::class,
        'amount_tax' => MoneyCast::class,
        'amount_total' => MoneyCast::class,
        'amount_subtotal' => MoneyCast::class,
        'amount_discount' => MoneyCast::class,
    ];
    
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // relación directa con producto (para items sin variantes)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDisplayImageAttribute()
    {
        // Si es una variante y tiene imagen
        if ($this->product_variant_id && $this->variant) {
            return $this->variant->getFirstMediaUrl('product-variant-image');
        }
        
        // Si no, usa la imagen del producto principal
        return $this->product?->image?->path ?? null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getResolvedProductAttribute()
    {
        return $this->product_variant_id 
            ? $this->variant->product 
            : $this->product;
    }
}
