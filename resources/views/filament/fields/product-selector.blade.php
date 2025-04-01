{{-- <div>
    <p style="margin-bottom: 20px;">Seleccionar Productos</p>
    
    @php
        $products = $getProducts();
        $productsCount = count($products);
    @endphp

    @if($productsCount > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            @foreach($products as $product)
                <div style="border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                    @if($product['image_url'])
                        <img 
                            src="{{ $product['image_url'] }}" 
                            alt="{{ $product['name'] }}"
                            style="width: 100%; height: 150px; object-fit: cover; margin-bottom: 8px;"
                        >
                    @else
                        <div style="width: 100%; height: 150px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                            <span style="color: #999;">Sin imagen</span>
                        </div>
                    @endif
                    
                    <h3 style="font-weight: bold; margin-bottom: 4px;">{{ $product['name'] }}</h3>
                </div>
            @endforeach
        </div>
    @else
        <div style="padding: 20px; background: #fff8e1; border: 1px solid #ffe0b2; border-radius: 4px;">
            No se encontraron productos disponibles.
        </div>
    @endif
</div> --}}

<div 
    x-data="{
        selectedProducts: @js($getState() ?? []),
        products: @js($getProducts()),
        search: '',
        
        get filteredProducts() {
            if (!this.search) return this.products;
            return this.products.filter(product => 
                product.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        toggleProduct(productId) {
            if (this.selectedProducts.includes(productId)) {
                this.selectedProducts = this.selectedProducts.filter(id => id !== productId);
            } else {
                this.selectedProducts.push(productId);
            }
            $wire.set('{{ $getStatePath() }}', this.selectedProducts);
        }
    }"
    style="padding: 1rem;"
>
    <input 
        type="text" 
        x-model="search" 
        placeholder="Buscar productos..."
        style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;"
    >

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        <template x-for="product in filteredProducts" :key="product.id">
            <div
                @click="toggleProduct(product.id)"
                :style="{
                    border: selectedProducts.includes(product.id) ? '2px solid #3b82f6' : '1px solid #ddd',
                    cursor: 'pointer'
                }"
                style="padding: 0.5rem; border-radius: 4px; transition: all 0.2s;"
            >
                <div style="height: 120px; overflow: hidden; margin-bottom: 0.5rem;">
                    <img 
                        x-show="product.image_url" 
                        :src="product.image_url" 
                        :alt="product.name"
                        style="width: 100%; height: 100%; object-fit: cover;"
                    >
                    <div 
                        x-show="!product.image_url" 
                        style="width: 100%; height: 100%; background: #f3f4f6; display: flex; align-items: center; justify-content: center;"
                    >
                        <span style="color: #9ca3af;">Sin imagen</span>
                    </div>
                </div>
                <div style="font-weight: 500;" x-text="product.name"></div>
                <div style="font-size: 0.75rem; color: #6b7280;">
                    <span x-show="selectedProducts.includes(product.id)" style="color: #3b82f6;">✓ Seleccionado</span>
                </div>
            </div>
        </template>
    </div>

    <input 
        type="hidden" 
        name="{{ $getName() }}"
        x-model="JSON.stringify(selectedProducts)"
        x-ref="hiddenInput"
    >
</div>