<?php

namespace App\Actions\Webshop;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CreateStripeCheckoutSession
{
    public function createFromCart(Cart $cart)
    {
        return $cart->user
            ->allowPromotionCodes()
            ->checkout(
                $this->formatCartItems($cart->items), [
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

    private function formatCartItems(Collection $items)
    {
        $taxRate = 0.16; // IVA del 16% en México
        $totalBeforeTax = 0; // Variable para calcular la base imponible
    
        $formattedItems = $items->loadMissing('product', 'variant.attributes')->map(function (CartItem $item) use (&$totalBeforeTax) {
            $basePrice = $item->product->price->getAmount(); // Precio base en centavos
            $totalBeforeTax += $basePrice * $item->quantity; // Acumular total sin impuestos
            
            // Obtener los atributos de la variante
            $attributesDescription = $item->variant->attributes->map(function ($attribute) {
                return "{$attribute->key}: {$attribute->value}";
            })->implode(' / ');
            
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
    
        // 🔥 ahora calculamos el IVA total
        $totalTax = (int) round($totalBeforeTax * $taxRate);
    
        // 🔥 agregar línea separada para los impuestos
        if ($totalTax > 0) {
            $formattedItems[] = [
                'price_data' => [
                    'currency' => 'MXN',
                    'unit_amount' => $totalTax, // IVA total en centavos
                    'product_data' => [
                        'name' => 'IVA (16%)', // Nombre visible en el checkout
                        'description' => 'Impuesto al Valor Agregado',
                    ]
                ],
                'quantity' => 1,
            ];
        }
    
        return $formattedItems;
    }
    
    
}