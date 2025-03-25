<?php

namespace App\Models;

use App\Models\Image;
use App\Casts\MoneyCast;
use App\Models\CartItem;
use Spatie\Image\Enums\Fit;
use App\Models\ProductVariant;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public $casts = [
        'price' => MoneyCast::class,
        'amount_tax' => MoneyCast::class,
        'amount_total' => MoneyCast::class,
        'amount_subtotal' => MoneyCast::class,
        'amount_discount' => MoneyCast::class,
        'is_admin' => 'boolean',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class)->ofMany('featured', 'max');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // calcular el stock total si el producto tiene "variants"
    public function getTotalStockAttribute()
    {
        if ($this->variants->isNotEmpty()) {
            // Si tiene variaciones, sumar el stock de todas las variaciones
            return $this->variants->sum('total_variant_stock');
        } else {
            // Si no tiene variaciones, usar el campo total_stock
            return $this->total_product_stock;
        }
    }

    public function updateStockFromVariants()
    {
        // si el producto tiene variantes, la suma dependerá del stock de las variantes
        if ($this->variants()->exists()) {
            $this->total_product_stock = $this->variants->sum('total_variant_stock');
        }
        $this->save();
        // si el stock total es 0, marcar el producto como no publicado 
        if ($this->total_product_stock <= 0) {
            $this->update(['is_published' => false]);
        }
    }

    public function decreaseStock($quantity)
    {
        // primero verificar si tiene variantes 
        if ($this->has_variants) {
            throw new \Exception("Este producto tiene variantes, disminuya el stock de la variante específica");
        }

        // si no tiene variantes, proceder con la disminución
        $this->decrement('total_product_stock', $quantity);

        if ($this->total_product_stock <= 0) {
            $this->update(['is_published' => false]);
        }
    }

    // colecciones, "featured" e "imagenes"
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile();
        $this->addMediaCollection('images');
    }

    // conversiones de imagenes para manejar diferentes tamaños
    public function registerMediaConversions(?Media $media = null): void
    {   
        $this
            ->addMediaConversion('sm_thumb')
            ->fit(Fit::Contain, 150, 150)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('md_thumb')
            ->fit(Fit::Contain, 300, 300)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('lg_thumb')
            ->fit(Fit::Contain, 1080, 1080)
            ->format('webp')
            ->nonQueued(); 
    }
}
