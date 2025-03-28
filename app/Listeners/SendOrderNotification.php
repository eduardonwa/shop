<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\OrderCreated;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $user = $order->user;

        // 1. enviar correo a administrador(es)
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($event->order));
        }

        // 2. email de confirmación al cliente
        Mail::to($user)->send(new OrderConfirmation($order));
    }
}
