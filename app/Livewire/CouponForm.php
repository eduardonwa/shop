<?php

namespace App\Livewire;

use App\Models\Coupon;
use Livewire\Component;

class CouponForm extends Component
{
    public $couponCode;
    public $discountApplied;
    public $context;
    public $targetId;

    public function applyCoupon()
    {
        $this->validate(['couponCode' => 'required|string|max:32']);

        $coupon = Coupon::where('code', $this->couponCode)
            ->when($this->context === 'product', fn($q) => 
                $q->whereHas('products', fn($q) =>
                    $q->where('products.id', $this->targetId))
            )
            ->valid()
            ->first();

        if ($coupon) {
            $this->discountApplied = true;
            $this->dispatch('couponApplied',
                code: $coupon->code,
                finalPrice: $coupon->applyDiscount($this->targetId)
            );
        } else {
            $this->addError('couponCode', 'Cupón no válido o expirado.');
        }
    }

    public function render()
    {
        return view('livewire.coupon-form');
    }
}
