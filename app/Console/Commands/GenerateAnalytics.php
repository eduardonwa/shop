<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\AnalyticsRecord;
use Illuminate\Console\Command;
use App\Notifications\DailyAnalyticsReport;

class GenerateAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-analytics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera reportes analíticos diarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        // datos para el email
        $reportData = [];

        // 1.productos más vendidos
        Product::withSum('orderItems', 'quantity')
            ->get()
            ->each(function ($product) {
                $product->update([
                    'cached_quantity_sold' => $product->order_items_sum_quantity
                ]);
                $reportdata['top_products'][] = [
                    'name' => $product->name,
                    'sold' => $product->order_items_sum_quantity
                ];
            });
        
        // 2. carritos abandonados (registra en tabla analytics)
        $abandonedCarts = Cart::whereDoesntHave('orders')
            ->where('created_at', '<', now()->subDay())
            ->count();
        
        AnalyticsRecord::create([
            'type' => 'abandoned_carts',
            'data' => ['count' => $abandonedCarts],
            'recorded_at' => now()
        ]);

        $reportData['abandoned_carts'] = $abandonedCarts;

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new DailyAnalyticsReport($reportData));
        }

        $this->info('Reportes generados exitosamente');
    }
}
