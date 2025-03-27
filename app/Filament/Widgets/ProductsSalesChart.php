<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;

class ProductsSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $items = OrderItem::with('product')
            ->selectRaw('product_id, sum(quantity) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
            
        return [
            'labels' => $items->pluck('product.name'),
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $items->pluck('total'),
                    'backgroundColor' => '#3B82F6',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
