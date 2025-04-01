<?php

namespace App\Filament\Fields;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Field;

class ProductSelector extends Field
{
    protected string $view = 'filament.fields.product-selector';

/*     protected ?array $products = null; */

/*     public function getProducts(): array
    {
        if ($this->products !== null) {
            return $this->products;
        }

        $products = Product::with(['media', 'collections'])
            ->limit(5)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->getFirstMediaUrl('featured', 'sm_thumb'),
                    'collections' => $product->collections->pluck('name')->join(', ')
                ];
            })->toArray();

            $this->products = $products;
        
        return $this->products;
    } */

/*     public function getProducts(): array
    {
        return Product::with(['media'])
            ->limit(50)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->getFirstMediaUrl('featured', 'sm_thumb')
                ];
            })
            ->toArray();
    } */

    protected function setUp(): void
    {
        parent::setUp();
    
        $this->afterStateHydrated(function (ProductSelector $component, $state) {
            $component->state($this->normalizeState($state));
        });
    
        $this->dehydrateStateUsing(fn ($state) => $this->normalizeState($state));
    }
    
    private function normalizeState($state): array
    {
        if (is_string($state)) {
            $state = json_decode($state, true) ?? [];
        }
        
        return array_values(array_filter((array) $state, fn($id) => is_numeric($id)));
    }

    public function getProducts(): array
    {
        return Product::with(['media'])
            ->whereHas('media') // productos con imágenes
            ->limit(100)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->getFirstMediaUrl('featured', 'sm_thumb')
                ];
            })
            ->toArray();
    }
}