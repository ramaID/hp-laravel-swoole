<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShowRealTimeMetricsController extends \App\Http\Controllers\Controller
{
    /**
     * Display the real-time metrics dashboard.
     */
    public function __invoke(Request $request)
    {
        // Get Swoole server stats if available
        $swooleStats = $this->getSwooleStats();

        // Get cache information
        $cacheStats = $this->getCacheStats();

        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('dashboard.real-time-metrics', [
            'swoole_stats' => $swooleStats,
            'cache_stats' => $cacheStats,
            'performance_metrics' => $performanceMetrics,
            'title' => 'Real-time Metrics - Laravel Octane + Swoole',
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

        // Return mock data for development
        return [
            'start_time' => time() - 3600, // 1 hour ago
            'connection_num' => rand(10, 50),
            'accept_count' => rand(1000, 5000),
            'close_count' => rand(900, 4500),
            'worker_num' => 4,
            'idle_worker_num' => rand(2, 4),
            'tasking_num' => rand(0, 10),
            'request_count' => rand(5000, 15000),
            'worker_request_count' => rand(1000, 3000),
            'coroutine_num' => rand(0, 100),
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

        // Count cached items
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
                'avg_response_time' => '~3s',
                'description' => 'Traditional synchronous processing',
                'color' => 'red',
            ],
            'concurrent' => [
                'avg_response_time' => '~1s',
                'description' => 'Parallel task execution with Octane',
                'color' => 'yellow',
            ],
            'cached' => [
                'avg_response_time' => '~111μs',
                'description' => 'In-memory cache with TTL',
                'color' => 'blue',
            ],
            'tick_cached' => [
                'avg_response_time' => '~85μs',
                'description' => 'Pre-warmed background cache',
                'color' => 'green',
            ],
        ];
    }
}
