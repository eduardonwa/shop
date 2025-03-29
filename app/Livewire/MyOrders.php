<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class MyOrders extends Component
{
    #[Computed]
    public function orders()
    {
        return Auth::user()->orders;
    }

    public function render()
    {
        return view('livewire.my-orders');
    }
}
