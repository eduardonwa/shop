<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class StoreFront extends Component
{
    use WithPagination;

    #[Url]
    public $searchQuery;

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->searchQuery, fn($query) => $query->where('name', 'like', "%{$this->searchQuery}%"))
            ->paginate(5);
    }

    public function render()
    {
        return view('livewire.store-front');
    }
}
