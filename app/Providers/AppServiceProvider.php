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
                $isNotSwoole = config('octane.server') !== 'swoole';

                if ($isNotSwoole) {
                    return;
                }

                // Use worker-specific cache key to prevent conflicts
                $workerId = getmypid(); // Use process ID as unique worker identifier
                $tickerKey = "dashboard-ticker-{$workerId}";
                $cacheStore = $this->getCacheStore();

                // Check if this worker already registered the ticker
                if (Cache::store($cacheStore)->has($tickerKey)) {
                    return;
                }

                try {
                    $callback = function () use ($cacheStore) {
                        Log::info('Ticker: Refreshing dashboard cache...');

                        try {
                            // Check if Event model exists and has the required methods
                            if (!class_exists(Event::class)) {
                                Log::warning('Ticker: Event model not found, skipping cache refresh');
                                return;
                            }

                            // Optimize queries with single query using raw SQL for better performance
                            $result = $this->fetchDashboardData();

                            $cacheKey = 'dashboard-tick-cache';
                            $cacheTtl = config('cache.dashboard_ttl', 300); // 5 minutes default

                            Cache::store($cacheStore)->put($cacheKey, $result, $cacheTtl);
                            Log::info('Ticker: Dashboard cache refreshed successfully', [
                                'total_events' => $result['count']
                            ]);
                        } catch (Exception $e) {
                            Log::error('Ticker: Failed to refresh dashboard cache', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    };
                    Octane::tick('cache-dashboard-query', $callback)
                        ->seconds(config('cache.dashboard_refresh_interval', 60))  // Configurable interval
                        ->immediate(); // Run immediately when worker starts

                    // Mark this worker as having registered the ticker
                    Cache::store($cacheStore)->put($tickerKey, true, 3600); // 1 hour TTL

                    Log::info('Ticker: Dashboard ticker registered successfully', [
                        'worker_id' => $workerId
                    ]);
                } catch (Exception $e) {
                    Log::error('Ticker: Failed to register dashboard ticker', [
                        'worker_id' => $workerId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        );
    }

    private function getCacheStore(): string
    {
        // Fallback to default cache store if octane store is not available
        $store = 'octane';

        try {
            Cache::store($store)->get('test');
            return $store;
        } catch (Exception $e) {
            Log::warning('Ticker: Octane cache store not available, falling back to default store');
            return config('cache.default', 'file');
        }
    }

    private function fetchDashboardData(): array
    {
        $events = Event::all();

        return [
            'count' => $events->count(),
            'eventsInfo' => $events->where('type', 'INFO')->count(),
            'eventsWarning' => $events->where('type', 'WARNING')->count(),
            'eventsAlert' => $events->where('type', 'ALERT')->count(),
            'last_updated' => now()->toISOString(),
        ];
    }
}
