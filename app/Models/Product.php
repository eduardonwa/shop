<?php

namespace App\Models;

use App\Models\Image;
use App\Casts\MoneyCast;
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

    // relacion para obtener la imagen "principal"
    public function productMedia()
    {
        return $this->belongsToMany(Media::class, 'media_product')
            ->withPivot('featured')
            ->withTimestamps();
    }

    // colecciones, "featured" e "imagenes"
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile();
        $this->addMediaCollection('images');
    }
}
