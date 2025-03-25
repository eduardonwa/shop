<?php

namespace App\Actions\Webshop;

use App\Factories\CartFactory;

class AddProductToCart
{
    public function add($productId, $variantId = null, $quantity = 1, $cart = null)
    {
        $cart = $cart ?: CartFactory::make();
        
        $item = $cart->items()->firstOrCreate([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
        ], [
            'quantity' => 0,
        ]);

        $item->increment('quantity', $quantity);
        $item->touch();
    }
}