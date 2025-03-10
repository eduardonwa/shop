<?php

namespace App\Actions\Webshop;

use App\Factories\CartFactory;

class AddProductVariantToCart
{
    public function add($variantId)
    {
        CartFactory::make()->items()->firstOrCreate([
            'product_variant_id' => $variantId,
        ], [
            'quantity' => 0,
        ])->increment('quantity');
    }
}