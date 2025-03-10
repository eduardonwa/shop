<?php

namespace App\Factories;

use App\Models\Cart;

class CartFactory
{
    public static function make(): Cart
    {
        /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth();

        return match($auth->guest()) {
            true => Cart::firstOrCreate(['session_id' => session()->getId()]),
            false => $auth->user()->cart ?: $auth->user()->cart()->create(),
        };
    }
}