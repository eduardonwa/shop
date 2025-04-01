<?php

namespace App\Filament\Forms\Components;

use App\Models\Product;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;

class ProductImageSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->multiple()
            ->options(function () {
                return Product::with(['media' => function($q) {
                    $q->where('collection_name', 'featured');
                }])->get()->mapWithKeys(function (Product $product) {
                    return [$product->id => $product->name];
                })->toArray();
            })
            ->getSearchResultsUsing(function (string $search) {
                return Product::with(['media' => function($q) {
                    $q->where('collection_name', 'featured');
                }])
                ->where('name', 'like', "%{$search}%")
                ->limit(50)
                ->get()
                ->mapWithKeys(function (Product $product) {
                    return [$product->id => $product->name];
                })->toArray();
            })
            ->required()
            ->preload()
            ->searchable()
            ->allowHtml()
            ->searchDebounce(500)
            ->view('filament.forms.components.product-image-select');
    }
}