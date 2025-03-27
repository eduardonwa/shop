<?php

namespace App\Actions\Webshop;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class AddProductToCart
{
    public function add($productId, $variantId = null, $quantity = 1, $cart = null, $couponCode = null)
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;
        
        $this->validateStock($product, $variant, $quantity, $cart);
    
        // Usar el carrito proporcionado o crear uno nuevo
        $cart = $cart ?: $this->getOrCreateCart();
        
        $this->addOrUpdateCartItem($cart, $product, $variant, $quantity);
    
        if ($couponCode) {
            $cart->update(['coupon_code' => $couponCode]);
        }
    }

    protected function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        
        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }

    protected function validateStock(Product $product, ?ProductVariant $variant, int $quantity, ?Cart $cart = null): void
    {
        $cart = $cart ?: $this->getOrCreateCart();
        
        if ($variant) {
            $inCart = $cart->items()
                ->where('product_variant_id', $variant->id)
                ->sum('quantity');
            
            $available = $variant->total_variant_stock - $inCart;

            throw_unless(
                $available >= $quantity,
                new \Exception("No hay suficiente stock. Disponibles: {$available}")
            );
        } else {
            $inCart = $cart->items()
                ->where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->sum('quantity');

            $available = $product->total_product_stock - $inCart;

            throw_unless(
                $available >= $quantity,
                new \Exception("No hay suficiente stock. Disponibles: {$available}")
            );
        }
    }

    protected function addOrUpdateCartItem(Cart $cart, Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->when($variant, fn($q) => $q->where('product_variant_id', $variant->id))
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $quantity,
            ]);
        }
    }
}