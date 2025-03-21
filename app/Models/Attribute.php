<?php

namespace App\Models;

use App\Models\AttributeVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    public function attributeVariants(): HasMany
    {
        return $this->hasMany(AttributeVariant::class);
    }
}
