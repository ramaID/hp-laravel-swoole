<?php

namespace App\Providers;

use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Facades\Octane;

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
        // Register ticker when using Octane
        $this->registerDashboardTicker();
    }

    private function registerDashboardTicker(): void
    {
        // Only register if Octane is available
        if (! class_exists('Laravel\Octane\Facades\Octane')) {
            return;
        }

        // Register using WorkerStarting event to ensure proper initialization
        $this->app->make('events')->listen(
            \Laravel\Octane\Events\WorkerStarting::class,
            function () {
                if (config('octane.server') !== 'swoole') {
                    return;
                }

                try {
                    Octane::tick('cache-dashboard-query', function () {
                        Log::info('Ticker: Refreshing dashboard cache...');

                        try {
                            $result = Octane::concurrently([
                                'count' => fn () => Event::query()->count(),
                                'eventsInfo' => fn () => Event::query()->ofType('INFO')->get(),
                                'eventsWarning' => fn () => Event::query()->ofType('WARNING')->get(),
                                'eventsAlert' => fn () => Event::query()->ofType('ALERT')->get(),
                            ]);

                            Cache::store('octane')->put('dashboard-tick-cache', $result, 300); // 5 minute TTL
                            Log::info('Ticker: Dashboard cache refreshed successfully');
                        } catch (Exception $e) {
                            Log::error('Ticker: Failed to refresh dashboard cache: '.$e->getMessage());
                        }
                    })
                        ->seconds(60)  // Run every 60 seconds
                        ->immediate(); // Run immediately when worker starts

                    Log::info('Ticker: Dashboard ticker registered successfully in worker');
                } catch (Exception $e) {
                    Log::error('Ticker: Failed to register dashboard ticker in worker: '.$e->getMessage());
                }
            }
        );
    }
}
