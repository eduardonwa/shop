<div class="grid grid-cols-2 gap-10 py-12">
    <div class="space-y-4">
        <div class="bg-white p-5 rounded-lg shadow">
            <img class="mx-auto" src="{{ $this->product->getFirstMediaUrl('featured', 'lg_thumb') }}" alt="Featured Image">
        </div>
    
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
        <div class="text-xl text-gray-700">{{ $this->product->price }}</div>

        <div class="mt-4">
            {{ $this->product->description }}
        </div>
    
        <div class="mt-4 space-y-4">
            @if ($this->product->variants->isNotEmpty())
                <select wire:model="variant" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-800">
                    @foreach ($this->product->variants as $variant)
                        <option value="{{ $variant->id }}">
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

            <x-button wire:click="addToCart"> Add to cart </x-button>
        </div>
    </div>
</div>
