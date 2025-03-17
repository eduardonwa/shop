<div class="mt-12">

    <div class="flex justify-between">
        <h1 class="text-xl font-medium">Our Products</h1>
        <div>
            <x-input wire:model.live.debounce="searchQuery" type="text" placeholder="Escribe algo"/>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mt-12">
        {{-- <p>{{ now()->isoFormat('DD MMMM YYYY') }}</p> --}}
        @foreach ($this->products as $product)
            <x-order-panel class="relative">
                <a
                    wire:navigate
                    href="{{ route('product', $product) }}"
                    class="absolute inset-0 w-full h-full"
                >
                    <img src="{{ $product->getFirstMediaUrl('featured') }}" class="rounded" alt="">
                    <h2 class="font-medium text-lg">{{ $product->name }}</h2>
                    <span class="text-gray-700 text-sm">{{ $product->price }}</span>
                </a>
            </x-order-panel>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $this->products->links() }}
    </div>
</div>
