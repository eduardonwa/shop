<?php

namespace App\Livewire;

use App\Actions\Webshop\AddProductVariantToCart;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class Product extends Component
{
    use InteractsWithBanner;

    public $productId;
    public $variant;
  
    public $rules = [
        'variant' => ['required', 'exists:App\Models\ProductVariant,id']
    ];

    protected function messages() 
    {
        return [
            'variant.exists' => 'La variante seleccionada no existe o es inválida.',
        ];
    }

    public function mount()
    {
        $this->variant = $this->product->variants()->value('id');
    }

    public function addToCart(AddProductVariantToCart $cart)
    {
        $this->validate();

        $cart->add(
            variantId: $this->variant
        );

        $this->banner('Tu producto se añadió al carrito.');
    }

    public function getProductProperty()
    {
        return \App\Models\Product::findOrFail($this->productId);
    }

    public function render()
    {
        return view('livewire.product');
    }
}
