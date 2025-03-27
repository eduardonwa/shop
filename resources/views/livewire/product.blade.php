<div class="grid grid-cols-2 gap-10 py-12">
    <div class="space-y-4">
        <div class="bg-white p-5 rounded-lg shadow">
            <img class="mx-auto" src="{{ $this->product->getFirstMediaUrl('featured', 'lg_thumb') }}" alt="Featured Image">
        </div>
        
        <!-- imagenes -->
        <div class="grid grid-cols-4 gap-4">
            @foreach ($this->product->getMedia('images') as $image)
                <div class="rounded bg-white p-2 rounded shadow">
                    <img src="{{ $image->getUrl('sm_thumb') }}" class="rounded" alt="">
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h1 class="text-3xl font-medium">{{ $this->product->name }}</h1>
        
        <!-- precio con/sin descuento-->
        <div class="text-xl text-gray-700">
            @if($discountApplied)
                <span class="line-through text-red-500">
                    ${{ number_format($originalPrice/100) }}
                </span>
                <span class="ml-2 text-green-600">
                    ${{ number_format($finalPrice/100) }}
                </span>
                @else
                    ${{ number_format($originalPrice/100) }}
            @endif
        </div>

        <div class="mt-4">
            {{ $this->product->description }}
        </div>

{{--         <div class="product-status product-status--{{ $this->product->stock_status }}">
            @switch($this->product->stock_status)
                @case('in_stock')
                    <span class="in-stock">Disponible</span>
                    @break
                @case('low_stock')
                    <span class="low-stock">Últimas unidades</span>
                    @break
                @case('sold_out')
                    <span class="sold-out">Agotado</span>
                    @break
            @endswitch
        </div> --}}

        <!-- formulario de cupón -->
        @unless($this->product->stock_status === 'sold_out')
            <div>
                <form wire:submit.prevent="applyCoupon">
                    <label for="couponCode" class="block text-sm font-medium text-gray-700 mb-1">
                        Código de descuento
                    </label>
                    <div class="flex">
                        <input
                            type="text"
                            id="couponCode"
                            wire:model="couponCode"
                            placeholder="Si tienes un cupón ingresalo aquí"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
        @endunless
        
        <!-- Selector de variante y checkout -->
        <div class="mt-4 space-y-4">
            @if ($this->product->variants->isNotEmpty())
                <select wire:model="variant" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-800">
                    @foreach ($this->product->variants as $variant)
                        <option value="{{ $variant->id }}"
                            @if($variant->total_variant_stock == 0) 
                                disabled
                                title="Producto agotado"
                                class="text-red-500 line-through"
                            @elseif ($variant->is_active == false)
                                style="display: none;"
                            @endif
                        >
                            @foreach ($variant->attributes as $attributeVariant)
                                {{ $attributeVariant->attribute->key . ':' ?? '' }} {{ $attributeVariant->value }}
                                @if (!$loop->last) / @endif
                            @endforeach
                        </option>
                    @endforeach
                </select>
            @endif
            
            @error('variant')
                <div class="mt-2 text-red-600">
                    {{ $message }}
                </div>
            @enderror

            @if($this->product->stock_status === 'sold_out')
                <x-button disabled class="cursor-not-allowed bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-600">AGOTADO</x-button>
            @else
                <x-button wire:click="addToCart">
                    @if($this->product->stock_status === 'low_stock')
                        ¡ÚLTIMAS UNIDADES! COMPRAR
                    @else
                        AÑADIR AL CARRITO
                    @endif
                </x-button>
            @endif
        </div>
    </div>
</div>
