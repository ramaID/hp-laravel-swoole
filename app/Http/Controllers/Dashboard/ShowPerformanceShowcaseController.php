<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShowPerformanceShowcaseController extends \App\Http\Controllers\Controller
{
    /**
     * Display the performance showcase dashboard.
     */
    public function __invoke(Request $request)
    {
        // Get Swoole server stats if available
        $swooleStats = $this->getSwooleStats();

        // Get cache information
        $cacheStats = $this->getCacheStats();

        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('dashboard.performance-showcase', [
            'swoole_stats' => $swooleStats,
            'cache_stats' => $cacheStats,
            'performance_metrics' => $performanceMetrics,
            'title' => 'High-Performance Laravel with Swoole - Performance Showcase',
        ]);
    }

    /**
     * Get Swoole server statistics
     */
    private function getSwooleStats(): array
    {
        try {
            if (app()->bound('swoole.server')) {
                $server = app('swoole.server');

                return $server->stats();
            }
        } catch (\Exception $e) {
            // Swoole not available
        }

        return [
            'start_time' => time(),
            'connection_num' => 0,
            'accept_count' => 0,
            'close_count' => 0,
            'worker_num' => 1,
            'idle_worker_num' => 1,
            'tasking_num' => 0,
            'request_count' => 0,
            'worker_request_count' => 0,
            'coroutine_num' => 0,
        ];
    }

    /**
     * Get cache statistics
     */
    private function getCacheStats(): array
    {
        $octaneCache = Cache::store('octane');

        $stats = [
            'tick_cache_exists' => $octaneCache->has('dashboard-tick-cache'),
            'events_cache_exists' => $octaneCache->has('dashboard-events-cache'),
            'total_cached_items' => 0,
        ];

        // Count cached items (if possible)
        try {
            $stats['total_cached_items'] = collect([
                'dashboard-tick-cache',
                'dashboard-events-cache',
            ])->filter(fn ($key) => $octaneCache->has($key))->count();
        } catch (\Exception $e) {
            // Cache driver doesn't support counting
        }

        return $stats;
    }

    /**
     * Get performance metrics comparison
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'sequential' => [
                'title' => 'Sequential',
                'avg_response_time' => '~3s',
                'description' => 'Traditional synchronous processing',
                'color' => 'red',
                'url' => route('dashboard.sequential'),
            ],
            'concurrent' => [
                'title' => 'Concurrent',
                'avg_response_time' => '~1s',
                'description' => 'Parallel task execution with Octane',
                'color' => 'yellow',
                'url' => route('dashboard.concurrent'),
            ],
            'cached' => [
                'title' => 'Cached',
                'avg_response_time' => '~111μs',
                'description' => 'In-memory cache with TTL',
                'color' => 'blue',
                'url' => route('dashboard.cached'),
            ],
            'tick_cached' => [
                'title' => 'Tick Cache',
                'avg_response_time' => '~85μs',
                'description' => 'Pre-warmed background cache',
                'color' => 'green',
                'url' => route('dashboard.tick-cached'),
            ],
        ];
    }
}
