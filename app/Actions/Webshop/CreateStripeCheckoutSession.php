<?php

namespace App\Actions\Webshop;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
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
                // activar/desactivar esta opción si la tienda realiza envios
                'shipping_address_collection' => [
                    'allowed_countries' => [
                        'US', 'MX'
                    ]
                ]
            ]
        );
    }

    private function formatCartItems(Collection $items)
    {
        $taxRate = 0.16; // IVA del 16% en México
        $totalBeforeTax = 0; // Variable para calcular la base imponible
    
        $formattedItems = $items->loadMissing('product', 'variant')->map(function (CartItem $item) use (&$totalBeforeTax) {
            $basePrice = $item->product->price->getAmount(); // Precio base en centavos
            $totalBeforeTax += $basePrice * $item->quantity; // Acumular total sin impuestos
    
            return [
                'price_data' => [
                    'currency' => 'MXN',
                    'unit_amount' => $basePrice, // 🔥 Precio unitario sin impuestos
                    'product_data' => [
                        'name' => $item->product->name,
                        'description' => "Size: {$item->variant->size} - Color: {$item->variant->color}",
                        'metadata' => [
                            'product_id' => $item->product->id,
                            'product_variant_id' => $item->product_variant_id,
                        ]
                    ]
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();
    
        // 🔥 Ahora calculamos el IVA total
        $totalTax = (int) round($totalBeforeTax * $taxRate);
    
        // 🔥 Agregar línea separada para los impuestos
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