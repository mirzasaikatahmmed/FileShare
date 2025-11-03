<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Automatically delete files older than 7 days
        // Runs daily at 2:00 AM
        $schedule->command('files:cleanup --days=7')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Log::info('File cleanup completed successfully');
            })
            ->onFailure(function () {
                \Log::error('File cleanup failed');
            });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
