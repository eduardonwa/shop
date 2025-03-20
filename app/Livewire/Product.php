<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Laravel\Jetstream\InteractsWithBanner;
use App\Actions\Webshop\AddProductVariantToCart;

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
        // obtiene el ID de la primera variante del producto
        $this->variant = $this->product->variants()->value('id');
    }

    public function addToCart(AddProductVariantToCart $cart)
    {
        $this->validate();

        $cart->add(
            variantId: $this->variant
        );

        $this->banner('Tu producto se añadió al carrito.');

        $this->dispatch('productAddedToCart');
    }

    #[Computed]
    public function product()
    {
        return \App\Models\Product::with('variants.attributes')->findOrFail($this->productId);
    }

    public function render()
    {
        return view('livewire.product');
    }
}
