<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/dashboard-sequential', Dashboard\ShowSequentialController::class)
    ->name('dashboard.sequential');
Route::get('/dashboard-concurrent', Dashboard\ShowConcurrentController::class)
    ->name('dashboard.concurrent');
Route::get('/dashboard-cached', Dashboard\ShowCachedController::class)
    ->name('dashboard.cached');
Route::get('/dashboard-tick-cached', Dashboard\ShowTickCachedController::class)
    ->name('dashboard.tick-cached');
Route::get('/performance-showcase', Dashboard\ShowPerformanceShowcaseController::class)
    ->name('dashboard.performance-showcase');
Route::get('/real-time-metrics', Dashboard\ShowRealTimeMetricsController::class)
    ->name('dashboard.real-time-metrics');

// Swoole stats route for monitoring
Route::get('/swoole-stats', function () {
    if (app()->bound('swoole.server')) {
        $server = app('swoole.server');

        return response()->json($server->stats());
    }

    return response()->json(['error' => 'Swoole server not available']);
});

// Manual trigger for testing ticker functionality
Route::get('/test-ticker', function () {
    try {
        $result = \Laravel\Octane\Facades\Octane::concurrently([
            'count' => fn () => \App\Models\Event::query()->count(),
            'eventsInfo' => fn () => \App\Models\Event::query()->ofType('INFO')->count(),
            'eventsWarning' => fn () => \App\Models\Event::query()->ofType('WARNING')->count(),
            'eventsAlert' => fn () => \App\Models\Event::query()->ofType('ALERT')->count(),
        ]);

        \Illuminate\Support\Facades\Cache::store('octane')->put('dashboard-tick-cache', $result, 120);

        return response()->json([
            'status' => 'success',
            'message' => 'Manually triggered ticker logic. Cache warmed successfully!',
            'data' => $result,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: '.$e->getMessage(),
        ], 500);
    }
});

Route::get('/', Dashboard\ShowPerformanceShowcaseController::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
