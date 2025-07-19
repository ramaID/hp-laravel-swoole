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
                $this->handleWorkerStarting();
            }
        );
    }

    private function handleWorkerStarting(): void
    {
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
            $this->registerOctaneTicker($cacheStore);
            $this->markWorkerAsRegistered($cacheStore, $tickerKey, $workerId);
        } catch (Exception $e) {
            $this->logTickerRegistrationError($workerId, $e);
        }
    }

    private function registerOctaneTicker(string $cacheStore): void
    {
        $callback = function () use ($cacheStore) {
            $this->refreshDashboardCache($cacheStore);
        };

        Octane::tick('cache-dashboard-query', $callback)
            ->seconds(config('cache.dashboard_refresh_interval', 60))  // Configurable interval
            ->immediate(); // Run immediately when worker starts
    }

    private function refreshDashboardCache(string $cacheStore): void
    {
        Log::info('Ticker: Refreshing dashboard cache...');

        try {
            // Optimize queries with single query using raw SQL for better performance
            $result = $this->fetchDashboardData();

            $cacheKey = 'dashboard-tick-cache';
            $cacheTtl = config('cache.dashboard_ttl', 300); // 5 minutes default

            Cache::store($cacheStore)->put($cacheKey, $result, $cacheTtl);
            Log::info('Ticker: Dashboard cache refreshed successfully', [
                'total_events' => $result['count'],
            ]);
        } catch (Exception $e) {
            Log::error('Ticker: Failed to refresh dashboard cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function markWorkerAsRegistered(string $cacheStore, string $tickerKey, int $workerId): void
    {
        // Mark this worker as having registered the ticker
        Cache::store($cacheStore)->put($tickerKey, true, 3600); // 1 hour TTL

        Log::info('Ticker: Dashboard ticker registered successfully', [
            'worker_id' => $workerId,
        ]);
    }

    private function logTickerRegistrationError(int $workerId, Exception $e): void
    {
        Log::error('Ticker: Failed to register dashboard ticker', [
            'worker_id' => $workerId,
            'error' => $e->getMessage(),
        ]);
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
        // $query = <<<SQL
        //     COUNT(*) as total_count,
        //     SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as info_count,
        //     SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as warning_count,
        //     SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as alert_count
        // SQL;

        // // Single optimized query to get counts by type
        // $eventCounts = Event::selectRaw($query, ['INFO', 'WARNING', 'ALERT'])->first();

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
