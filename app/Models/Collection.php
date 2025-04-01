<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
