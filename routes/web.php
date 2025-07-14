<?php

use App\Http\Controllers\ShowConcurrentDashboardController;
use App\Http\Controllers\ShowSequentialDashboardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/dashboard-sequential', ShowSequentialDashboardController::class)
    ->name('dashboard.sequential');
Route::get('/dashboard-concurrent', ShowConcurrentDashboardController::class)
    ->name('dashboard.concurrent');

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
