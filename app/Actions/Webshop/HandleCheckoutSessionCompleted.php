<?php

namespace App\Actions\Webshop;

use Exception;
use App\Models\Cart;
use App\Models\User;
use Stripe\LineItem;
use App\Models\OrderItem;
use Laravel\Cashier\Cashier;
use App\Models\ProductVariant;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleCheckoutSessionCompleted
{
    public function handle($sessionId)
    {
        DB::transaction(function() use ($sessionId) {
            // recuperar la sesión con los items
        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId, [
                'expand' => ['line_items.data.price.product']
            ]);

            $totalTax = 0;
            $subtotal = 0;

            // Log para verificar los datos de la sesión
            Log::info('Sesión de Stripe recuperada:', [
                'session_id' => $session->id,
                'metadata' => $session->metadata,
                'line_items' => $session->line_items,
            ]);

            // obtener el usuario y el carrito de los metadatos
            $user = User::find($session->metadata->user_id);
            $cart = Cart::find($session->metadata->cart_id);

            Log::info('Usuario y carrito recuperados:', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
            ]);

            foreach ($session->line_items->data as $lineItem) {
                // Verificar que el producto no sea nulo
                $product = $lineItem->price->product ?? null;
                if (!$product) {
                    Log::error('Ítem sin producto:', [
                        'line_item_id' => $lineItem->id,
                        'description' => $lineItem->description,
                    ]);
                    continue; // Ignorar ítems sin producto
                }
            
                // Verificar que amount_total no sea nulo
                if ($lineItem->amount_total === null) {
                    Log::error('Ítem sin amount_total:', [
                        'line_item_id' => $lineItem->id,
                        'description' => $lineItem->description,
                    ]);
                    continue; // Ignorar ítems sin amount_total
                }
            
                // Obtener el id del producto desde los metadatos del precio
                $isTax = $product->metadata->is_tax ?? false;
            
                if ($isTax) {
                    // Sumar el IVA al total
                    $totalTax += $lineItem->amount_total;
                    Log::info('Item con IVA:', [
                        'line_item_id' => $lineItem->id,
                        'description' => $lineItem->description,
                        'amount' => $lineItem->amount_total,
                    ]);
                    continue; // Continuar con el siguiente ítem
                }
            
                // Sumar al subtotal (solo productos)
                $subtotal += $lineItem->amount_total;
            
                // Obtener el variantId
                $variantId = $product->metadata->product_variant_id ?? null;
            
                if (!$variantId) {
                    Log::error('Ítem sin variant_id:', [
                        'line_item_id' => $lineItem->id,
                        'description' => $lineItem->description,
                    ]);
                    continue; // Ignorar ítems sin variant_id
                }
            
                $quantity = $lineItem->quantity;
            
                Log::info('Ítem procesado:', [
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'line_item' => $lineItem,
                ]);
            
                // Disminuir el stock de la variante
                $variant = ProductVariant::findOrFail($variantId);
                $variant->decreaseStock($quantity);
            
                Log::info('Stock disminuido:', [
                    'variant_id' => $variant->id,
                    'new_stock' => $variant->stock,
                ]);
            
                // Log para verificar los valores actualizados
                Log::info('Valores actualizados:', [
                    'subtotal' => $subtotal,
                    'total_tax' => $totalTax,
                    'total' => $subtotal + $totalTax,
                ]);
            }

            Log::info('Transacción completada con éxito.');
            
        } catch (\Exception $e) {
            Log::error('Error en HandleCheckoutSessionCompleted:', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            // Relanzar la excepción para que la transacción se revierta
            throw $e;
        }
            //logger('Cart encontrado:', ['cart_id' => $session->metadata->cart_id, 'cart' => $cart]);
            
            $order = $user->orders()->create([
                'stripe_checkout_session_id'    => $session->id,
                'amount_shipping'               => $session->total_details->amount_shipping,
                'amount_discount'               => $session->total_details->amount_discount,
                'amount_tax'                    => $totalTax,
                'amount_subtotal'               => $subtotal,
                'amount_total'                  => $subtotal + $totalTax,
                'billing_address' => [
                    'name'          => $session->customer_details->name,
                    'city'          => $session->customer_details->address->city,
                    'country'       => $session->customer_details->address->country,
                    'line1'         => $session->customer_details->address->line1,
                    'line2'         => $session->customer_details->address->line2,
                    'postal_code'   => $session->customer_details->address->postal_code,
                    'state'         => $session->customer_details->address->state,
                ],
                'shipping_address' => [
                    'name'          => $session->shipping_details->name,
                    'city'          => $session->shipping_details->address->city,
                    'country'       => $session->shipping_details->address->country,
                    'line1'         => $session->shipping_details->address->line1,
                    'line2'         => $session->shipping_details->address->line2,
                    'postal_code'   => $session->shipping_details->address->postal_code,
                    'state'         => $session->shipping_details->address->state,
                ]
            ]);
    
            $lineItems = Cashier::stripe()->checkout->sessions->allLineItems($session->id);
    
            $orderItems = collect($lineItems->all())->map(function(LineItem $line) {
                $product = Cashier::stripe()->products->retrieve($line->price->product);
                
                // Verificar que product_variant_id no sea nulo
                if (empty($product->metadata->product_variant_id)) {
                    Log::error('Product variant ID is missing for product:', ['product' => (array) $product]);
                    return null; // O manejar el error de otra manera
                }

                // Obtener la variante y sus atributos
                $variant = ProductVariant::with('attributes')->find($product->metadata->product_variant_id);
                $attributesDescription = $variant->attributes->map(function ($attributeVariant) {
                    return "{$attributeVariant->attribute->key}: {$attributeVariant->value}";
                })->implode(' / ');

                return new OrderItem([
                    'product_variant_id'    => $product->metadata->product_variant_id,
                    'name'                  => $product->name,
                    'description'           => $attributesDescription,
                    'price'                 => $line->price->unit_amount, 
                    'quantity'              => $line->quantity,
                    'amount_discount'       => $line->amount_discount,
                    'amount_subtotal'       => $line->amount_subtotal,
                    'amount_tax'            => $line->amount_tax,
                    'amount_total'          => $line->amount_total,
                ]);
            })->filter(); // filtrar elementos nulos si es necesario
    
            $order->items()->saveMany($orderItems);

            $cart->items()->delete();
            $cart->delete();

            Mail::to($user)->send(new OrderConfirmation($order));
        });
    }
}