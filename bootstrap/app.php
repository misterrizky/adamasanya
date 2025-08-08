<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'forbid-banned-user' => \Cog\Laravel\Ban\Http\Middleware\ForbidBannedUser::class,
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'logs-out-banned-user' => \Cog\Laravel\Ban\Http\Middleware\LogsOutBannedUser::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/api/push-subscribe',
            'views/livewire/*',
            'views/pages/*',
        ]);

    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Tambahkan scheduled tasks di sini
        $schedule->command('payments:check-status')->everyFiveMinutes();
        $schedule->command('inspire')->hourly();
        
        // Atau bisa juga memanggil closure langsung
        $schedule->call(function () {
            // Logika pengecekan langsung di sini
            \Illuminate\Support\Facades\Log::info('Scheduled task executed at: '.now());
        })->dailyAt('03:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
