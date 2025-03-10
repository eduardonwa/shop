<?php

namespace App\Actions\Webshop;

use App\Models\Cart;

class AddProductVariantToCart
{
    public function add($variantId)
    {
        /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth();
        /** @var \App\Models\User|null $user */
        $user = $auth->user();

        $cart = match($auth->guest()) {
            true => Cart::firstOrCreate(['session_id' => session()->getId()]), 
            false => $cart = $user->cart ?: $user->cart()->create(),
        };
        
        $cart->items()->create([
            'product_variant_id' => $variantId,
            'quantity' => 1,
        ]);
    }
}