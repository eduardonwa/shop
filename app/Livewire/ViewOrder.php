<?php

namespace App\Livewire;

use Livewire\Component;

class ViewOrder extends Component
{
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderProperty()
    {
        return auth()->user()->orders()->findOrFail($this->orderId);
    }

    public function render()
    {
        return view('livewire.view-order');
    }
}
