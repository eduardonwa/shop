<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsRecord;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AbandonedCartStats extends BaseWidget
{
    protected function getStats(): array
    {
        $latest = AnalyticsRecord::where('type', 'abandoned_carts')
            ->latest()
            ->first();

        return [
            Stat::make('Carritos abandonados', $latest?->data['count'] ?? 0)
                ->description('Últimas 24 horas')
                ->chart($this->getChartData())
                ->color('danger'),
        ];
    }

    public function getChartData(): array
    {
        // datos de los últimos 7 días
        return AnalyticsRecord::where('type', 'abandoned_carts')
            ->orderBy('recorded_at')
            ->limit(7)
            ->pluck('data->count')
            ->toArray();
    }
}
