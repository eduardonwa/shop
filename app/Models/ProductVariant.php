<?php

namespace App\Models;

use App\Models\AttributeVariant;
use Illuminate\Support\Facades\DB;
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

    public function decreaseStock(int $quantity): void
    {
        DB::transaction(function () use ($quantity) {
            $this->update([
                'total_variant_stock' => max($this->total_variant_stock - $quantity, 0)
            ]);
            
            $this->product->updateStockFromVariants();
            $this->product->updateStockStatus(); // Actualiza el estado del producto padre
        });
    }

    public function setIsActiveAttribute($value)
    {
        // No permitir activar si no hay stock
        if ($value && $this->total_variant_stock <= 0) {
            return;
        }
        
        // No permitir desactivar si hay stock positivo
        if (!$value && $this->total_variant_stock > 0) {
            throw new \Exception("No se puede desactivar una variante con stock disponible");
        }
        
        $this->attributes['is_active'] = $value;
    }

    public function canFulfillOrder(int $quantity): bool
    {
        return $this->total_variant_stock >= $quantity;
    }

    public function reserveStock(int $quantity): void
    {
        throw_if(
            !$this->canFulfillOrder($quantity),
            new \RuntimeException('No hay suficiente stock disponible')
        );
        
        $this->decrement('total_variant_stock', $quantity);
        $this->refresh();
        
        if ($this->total_variant_stock <= 0) {
            $this->update(['is_active' => false]);
        }
        
        $this->product->updateStockFromVariants();
    }

    public function isAvailable()
    {
        return $this->is_active && $this->total_variant_stock > 0;
    }

    public function canFulfill(int $quantity): bool
    {
        return $this->is_active && $this->total_variant_stock >= $quantity;
    }
}
