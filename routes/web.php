<?php

use App\Mail\AbandonedCartReminder;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\StoreFront::class)->name('home');
Route::get('/product/{productId}', \App\Livewire\Product::class)->name('product');
Route::get('/cart', \App\Livewire\Cart::class)->name('cart');

Route::get('/preview', function() {
    $cart = \App\Models\User::first()->cart;

    return new AbandonedCartReminder($cart);
});
//Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/checkout-status', \App\Livewire\CheckoutStatus::class)->name('checkout-status');
    Route::get('/order/{orderId}', \App\Livewire\ViewOrder::class)->name('view-order');
    Route::get('/my-orders', \App\Livewire\MyOrders::class)->name('my-orders');
});