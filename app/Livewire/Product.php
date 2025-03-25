<?php

namespace App\Livewire;

use App\Models\Coupon;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Actions\Webshop\AddProductToCart;
use Laravel\Jetstream\InteractsWithBanner;

class Product extends Component
{
    use InteractsWithBanner;

    public $productId;
    public $variant;
    public $couponCode;
    public $discountApplied = false;
    public $originalPrice;
    public $finalPrice;
    public $discountAmount = 0;
  
    public $rules = [
        'variant' => ['nullable', 'exists:App\Models\ProductVariant,id'],
        'couponCode' => ['nullable', 'string', 'max:32'],
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
        $this->originalPrice = $this->product->price->getAmount();
        $this->finalPrice = $this->originalPrice;
    }

    public function applyCoupon()
    {
        $this->validate(['couponCode' => 'required|string|max:32']);

        // buscar cupón válido para este producto
        $coupon = Coupon::where('code', $this->couponCode)
            ->whereHas('products', fn($q) => $q->where('products.id', $this->productId))
            ->first();

        if ($coupon && $coupon->isValid()) {
            $this->discountAmount = $this->originalPrice - $coupon->applyDiscount($this->originalPrice);
            $this->finalPrice = $this->originalPrice - $this->discountAmount;
            $this->discountApplied = true;
            $this->banner('Cupón aplicado correctamente');
        } else {
            $this->discountApplied = false;
            $this->finalPrice = $this->originalPrice;
            $this->discountAmount = 0;
            $this->addError('couponCode', 'Cupón no válido o expirado.');
        }
    }

    public function addToCart(AddProductToCart $cart)
    {
        $this->validate();

        $cart->add(
            productId: $this->productId,
            variantId: $this->variant,
            couponCode: $this->discountApplied ? $this->couponCode : null
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
