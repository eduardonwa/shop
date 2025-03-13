<?php

namespace App\Actions\Webshop;

use Exception;
use App\Models\Cart;
use App\Models\User;
use Stripe\LineItem;
use App\Models\OrderItem;
use Laravel\Cashier\Cashier;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleCheckoutSessionCompleted
{
    public function handle($sessionId)
    {
        DB::transaction(function() use ($sessionId) {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
            $user = User::find($session->metadata->user_id);
            $cart = Cart::find($session->metadata->cart_id);
            //logger('Cart encontrado:', ['cart_id' => $session->metadata->cart_id, 'cart' => $cart]);
            
            $order = $user->orders()->create([
                'stripe_checkout_session_id'    => $session->id,
                'amount_shipping'               => $session->total_details->amount_shipping,
                'amount_discount'               => $session->total_details->amount_discount,
                'amount_tax'                    => $session->total_details->amount_tax,
                'amount_subtotal'               => $session->amount_subtotal,
                'amount_total'                  => $session->amount_total,
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

                return new OrderItem([
                    'product_variant_id'    => $product->metadata->product_variant_id,
                    'name'                  => $product->name,
                    'description'           => $product->description,
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