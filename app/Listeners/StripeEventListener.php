<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use App\Actions\Webshop\HandleCheckoutSessionCompleted;

class StripeEventListener
{
    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        // Verifica si el evento es "checkout.session.completed"
        if (isset($event->payload['type']) && $event->payload['type'] === 'checkout.session.completed') {
            // Verifica si el payload tiene el ID de la sesión
            if (isset($event->payload['data']['object']['id'])) {
                $sessionId = $event->payload['data']['object']['id'];

                // Llama a la acción HandleCheckoutSessionCompleted
                (new HandleCheckoutSessionCompleted())->handle($sessionId);
            }
        }
    }
}