<?php

namespace App\Actions\Webshop;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CreateStripeCheckoutSession
{
    public function createFromCart(Cart $cart)
    {

        $coupon = $cart->coupon_code 
            ? Coupon::where('code', $cart->coupon_code)->first()
            : null;

        return $cart->user
            ->allowPromotionCodes()
            ->checkout(
                $this->formatCartItems($cart->items, $coupon), [
                    'automatic_tax' => ['enabled' => false], // desactivar Stripe Tax
                    'customer_update' => [
                        'shipping' => 'auto',
                    ],
                    // activar/desactivar esta opción si la tienda realiza envios a otro pais
                    'shipping_address_collection' => [
                        'allowed_countries' => ['US', 'MX']
                    ],
                    'success_url' => route('checkout-status') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('cart'),
                    'metadata' => [
                        'user_id' => $cart->user->id,
                        'cart_id' => $cart->id,
                    ],
                ]
        );
    }

    private function formatCartItems(Collection $items, $coupon)
    {
        $taxRate = 0.16; // IVA del 16% en México
        $subtotal = 0; // subtotal sin impuestos
    
        $formattedItems = $items->loadMissing('product', 'variant.attributes')->map(function (CartItem $item) use (&$subtotal, $coupon) {
            $basePrice = $item->product->price->getAmount(); // Precio base en centavos
            
            if ($basePrice === null) {
                throw new \Exception("El precio base del producto no está definido.");
            }

            // aplicar descuento si el cupón fue válido para ese producto
            if ($coupon && $coupon->products->contains($item->product)) {
                $basePrice = $coupon->applyDiscount($basePrice);
            }

            $subtotal += $basePrice * $item->quantity; // Acumular total sin impuestos y cupón si aplica
            
            // Obtener los atributos de la variante
            $attributesDescription = $item->variant
                ? $item->variant->attributes->map(fn($av) => "{$av->attribute->key}: {$av->value}")->implode('/')
                : 'Producto estándar';
            
            return [
                'price_data' => [
                    'currency' => 'MXN',
                    'unit_amount' => $basePrice,
                    'product_data' => [
                        'name' => $item->product->name,
                        'description' => $attributesDescription,
                        'metadata' => [
                            'product_id' => $item->product->id,
                            'product_variant_id' => $item->product_variant_id,
                        ]
                    ]
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();
        
        if ($subtotal < 0) {
            throw new \Exception("El subtotal no puede ser negativo.");
        }
        
        // 🔥 ahora calculamos el IVA total
        $totalTax = (int) round($subtotal * $taxRate);
        
        // 🔥 agregar línea separada para los impuestos
        if ($totalTax > 0) {
            $formattedItems[] = [
                'price_data' => [
                    'currency' => 'MXN',
                    'unit_amount' => $totalTax, // IVA total en centavos
                    'product_data' => [
                        'name' => 'IVA (16%)', // Nombre visible en el checkout
                        'description' => 'Impuesto al Valor Agregado',
                        'metadata' => [
                            'is_tax' => true,
                        ],
                    ],
                ],
                'quantity' => 1,
            ];
        }

        \Log::info('Valores calculados:', [
            'subtotal' => $subtotal,
            'total_tax' => $totalTax,
            'total' => $subtotal + $totalTax,
        ]);
    
        return $formattedItems;
    } 
}