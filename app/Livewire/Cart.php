<?php

namespace App\Livewire;

use Livewire\Component;
use App\Factories\CartFactory;

class Cart extends Component
{

    public function getItemsProperty()
    {
        return CartFactory::make()->items;
    }
    
    public function render()
    {
        return view('livewire.cart');
    }
}
