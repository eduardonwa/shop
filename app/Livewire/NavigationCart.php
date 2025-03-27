<?php

namespace App\Livewire;

use Livewire\Component;
use App\Factories\CartFactory;
use Livewire\Attributes\Computed;

class NavigationCart extends Component
{

    public $listeners = [
        'productAddedToCart' => '$refresh',
        'productRemovedFromCart' => '$refresh',
        'cartUpdated' => 'updateCartCount'
    ];

    #[Computed]
    public function count()
    {
        return CartFactory::make()->items()->sum('quantity');
    }

    public function updateCartCount()
    {
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.navigation-cart');
    }
}
