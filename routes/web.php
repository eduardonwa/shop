<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;
use App\Mail\OrderConfirmation;
use App\Models\Order;

Route::get('/', \App\Livewire\StoreFront::class)->name('home');
Route::get('/product/{productId}', \App\Livewire\Product::class)->name('product');
Route::get('/cart', \App\Livewire\Cart::class)->name('cart');
Route::get('/checkout-status', \App\Livewire\CheckoutStatus::class)->name('checkout-status');
Route::get('/preview', function() {
    $order = Order::first();

    return new OrderConfirmation($order);
});
//Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
/* Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
}); */