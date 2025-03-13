<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;
use App\Mail\AbandonedCartReminder;
use Illuminate\Support\Facades\Mail;

class AbandonedCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:abandoned-cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Look for abandoned carts and notify their owner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carts = Cart::withWhereHas('user')->whereDate('updated_at', today()->subDay())->get();

        foreach($carts as $cart) {
            Mail::to($cart->user)->send(new AbandonedCartReminder($cart));
        }
    }
}
