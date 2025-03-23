<?php

namespace App\Models;

use App\Models\Image;
use App\Casts\MoneyCast;
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

    public function updateTotalStock()
    {
        $this->total_stock = $this->variants->sum('stock');
        $this->save();

        if ($this->total_stock <= 0) {
            $this->update(['is_published' => false]);
        }
    }

    // calcular el stock total si el producto tiene "variants"
    public function getTotalStockAttribute()
    {
        if ($this->variants->isNotEmpty()) {
            // Si tiene variaciones, sumar el stock de todas las variaciones
            return $this->variants->sum('stock');
        } else {
            // Si no tiene variaciones, usar el campo total_stock
            return $this->total_stock;
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
