<?php

namespace App\Actions\Webshop;

use App\Factories\CartFactory;

class AddProductToCart
{
    public function add($productId, $variantId = null, $quantity = 1, $cart = null, $couponCode = null)
    {
        $cart = $cart ?: CartFactory::make();
        
        $item = $cart->items()->firstOrCreate([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
        ], [
            'quantity' => 0,
        ]);

        if ($couponCode) {
            $cart->update(['coupon_code' => $couponCode]);
        }

        $item->increment('quantity', $quantity);
        $item->touch();
    }
}