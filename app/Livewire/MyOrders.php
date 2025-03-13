<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;

class MyOrders extends Component
{
    #[Computed]
    public function orders()
    {
        return auth()->user()->orders;
    }

    public function render()
    {
        return view('livewire.my-orders');
    }
}
