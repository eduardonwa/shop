<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\StoreFront::class)->name('home');
Route::get('/product/{productId}', \App\Livewire\Product::class)->name('product');

/* Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
}); */
