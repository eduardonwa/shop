<?php

namespace App\Livewire;

use Livewire\Component;

class StoreFront extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.store-front');
    }
}
