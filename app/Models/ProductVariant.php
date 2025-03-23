<?php

namespace App\Models;

use App\Models\AttributeVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(AttributeVariant::class);
    }

    public function decreaseStock($quantity)
    {
        if ($this->total_variant_stock >= $quantity) {
            $this->total_variant_stock -= $quantity;
            $this->save();

            if ($this->stock <= 0) {
                $this->update(['total_variant_stock' => 0]);
            }

            // actualiza el stock total del producto
            $this->product->updateTotalProductStock();
        } else {
            throw new \Exception('No hay suficiente stock disponible.');
        }
    }

    public function isAvailable()
    {
        return $this->total_variant_stock > 0;
    }
}
