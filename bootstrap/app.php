<?php

use App\Models\Transaction\Rent;
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
        // $schedule->command('inspire')->hourly();
        
        // Atau bisa juga memanggil closure langsung
        $schedule->call(function () {
            Rent::where('status', 'active')
                ->where('end_date', '<', now())
                ->get()
                ->each(function ($rent) {
                    $lateDays = now()->diffInDays($rent->end_date);
                    $lateFee = $lateDays * $rent->total_amount * 0.05;
                    $rent->user->charge($lateFee, 'late_fee');
                    $rent->status = 'overdue';
                    $rent->save();
                    activity()->log('Late fee charged for rent ID: ' . $rent->id);
                });
        })->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
