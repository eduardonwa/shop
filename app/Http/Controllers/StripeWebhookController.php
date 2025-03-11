<?php

namespace App\Http\Controllers;

use Stripe\Webhook;
use Illuminate\Http\Request;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (SignatureVerificationException $e) {
            return response('Firma inválida', 403);
        }

        event(new WebhookReceived($event->data['object']));

        return response('Webhook recibido', 200);
    }
}
