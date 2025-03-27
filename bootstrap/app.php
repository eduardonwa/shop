<?php

use Illuminate\Foundation\Application;
use App\Console\Commands\AbandonedCart;
use App\Console\Commands\GenerateAnalytics;
use App\Http\Middleware\CheckAdminRole;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\RemoveInactiveSessionCarts;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens([
            'stripe/webhook',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // mandar mensaje a la 1 para los que dejaron algo en su carrito
        $schedule->command(AbandonedCart::class)->dailyAt('13:00');
        // limpiar carritos vacios cada semana
        $schedule->command(RemoveInactiveSessionCarts::class)->weekly();
        // generar reporte semanal
        $schedule->command(GenerateAnalytics::class)->dailyAt('23:55');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
