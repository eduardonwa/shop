<div>
    <form wire:submit.prevent="applyCoupon">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Código de descuento
        </label>
        <div class="flex">
            <input
                type="text"
                wire:model="couponCode"
                placeholder="@if($context === 'product') Cupón para este producto @else Cupón para el carrito @endif"
                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
            >
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700"
            >
                Aplicar
            </button>
        </div>
        @error('couponCode')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror

        @if ($discountApplied)
            <p class="mt-2 text-sm text-green-600">
                ¡Cupón aplicado correctamente!
            </p>
        @endif
    </form>
</div>