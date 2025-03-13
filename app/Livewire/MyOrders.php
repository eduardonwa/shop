<?php

namespace App\Livewire;

use Livewire\Component;

class MyOrders extends Component
{

    public function getOrdersProperty()
    {
        return auth()->user()->orders;
    }

    public function render()
    {
        return view('livewire.my-orders');
    }
}
