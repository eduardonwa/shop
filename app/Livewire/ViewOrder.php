<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class ViewOrder extends Component
{
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }
    
    #[Computed]
    public function order()
    {
        return Auth::user()->orders()
            ->with([
                'items.product',
                'items.variant.attributes.attribute',
                'items.variant.product',
                'coupon'
            ])
            ->findOrFail($this->orderId);
    }

    public function render()
    {
        return view('livewire.view-order');
    }
}
