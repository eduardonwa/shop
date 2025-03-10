<?php

namespace App\Providers;

use Money\Money;
use NumberFormatter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Money\Currencies\ISOCurrencies;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Money\Formatter\IntlMoneyFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Establece el locale para la aplicación
        app()->setLocale('es_MX');

        // Establece el locale para Carbon (manejo de fechas)
        Carbon::setLocale('es_MX');

        // Configura el locale para PHP (necesario para formatos de fecha)
        setlocale(LC_TIME, 'es_MX.utf8'); // Usa el locale instalado en el sistema

        // Configura el formateador de dinero
        Blade::stringable(function (Money $money) {
            $currencies = new ISOCurrencies();
            $numberFormatter = new NumberFormatter('es_MX', \NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
            
            return $moneyFormatter->format($money);
        });
    }
}
