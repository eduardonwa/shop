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

    public function decreaseStock(int $quantity): void
    {
        throw_if(
            $this->total_variant_stock < $quantity,
            new \RuntimeException('No hay suficiente stock disponible.')
        );
    
        $this->update([
            'total_variant_stock' => max($this->total_variant_stock - $quantity, 0)
        ]);
    
        $this->product->updateStockFromVariants();
    }

/*     public function decreaseStock($quantity)
    {
        // Verifica si hay suficiente stock
        if ($this->total_variant_stock < $quantity) {
            throw new \Exception('No hay suficiente stock disponible.');
        }
    
        // Disminuye el stock
        $this->total_variant_stock -= $quantity;
    
        // Asegura que el stock no sea negativo
        if ($this->total_variant_stock < 0) {
            $this->total_variant_stock = 0;
        }
    
        // Guarda los cambios en la base de datos
        $this->save();
    
        // Actualiza el stock total del producto asociado
        $this->product->updateStockFromVariants();
    } */

    public function isAvailable()
    {
        return $this->total_variant_stock > 0;
    }
}
