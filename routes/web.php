<?php

use App\Http\Controllers\ShowCachedDashboardController;
use App\Http\Controllers\ShowConcurrentDashboardController;
use App\Http\Controllers\ShowSequentialDashboardController;
use App\Http\Controllers\ShowTickCachedDashboardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/dashboard-sequential', ShowSequentialDashboardController::class)
    ->name('dashboard.sequential');
Route::get('/dashboard-concurrent', ShowConcurrentDashboardController::class)
    ->name('dashboard.concurrent');
Route::get('/dashboard-cached', ShowCachedDashboardController::class)
    ->name('dashboard.cached');
Route::get('/dashboard-tick-cached', ShowTickCachedDashboardController::class)
    ->name('dashboard.tick-cached');

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
            'eventsInfo' => fn () => \App\Models\Event::query()->ofType('INFO')->get(),
            'eventsWarning' => fn () => \App\Models\Event::query()->ofType('WARNING')->get(),
            'eventsAlert' => fn () => \App\Models\Event::query()->ofType('ALERT')->get(),
        ]);

        \Illuminate\Support\Facades\Cache::store('octane')->put('dashboard-tick-cache', $result, 120);

        return 'Manually triggered ticker logic. Cache warmed successfully!';
    } catch (Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
