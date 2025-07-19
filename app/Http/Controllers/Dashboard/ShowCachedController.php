<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Facades\Octane;

class ShowCachedController extends \App\Http\Controllers\Controller
{
    /**
     * Handle the incoming request with caching.
     */
    public function __invoke(Request $request)
    {
        $time = hrtime(true);

        try {
            // We wrap our entire concurrent operation in a cache block.
            [$count, $eventsInfo, $eventsWarning, $eventsAlert] =
            Cache::store('octane')->remember(
                key: 'dashboard-events-cache', // A unique key for our cache item
                ttl: 20, // Time-to-live in seconds
                callback: function () {
                    // This function only runs if the cache is empty or expired.
                    return Octane::concurrently([
                        fn () => Event::query()->count(),
                        fn () => Event::query()->ofType('INFO')->count(),
                        fn () => Event::query()->ofType('WARNING')->count(),
                        fn () => Event::query()->ofType('ALERT')->count(),
                    ]);
                }
            );
        } catch (Exception $e) {
            // Handle potential exceptions from the cache or concurrent tasks
            return response('Error: '.$e->getMessage(), 500);
        }

        $time = (hrtime(true) - $time) / 1_000_000;

        // Format time for better readability
        $formattedTime = $this->formatExecutionTime($time);

        return view('dashboard.default', [
            'info_count' => $eventsInfo,
            'warning_count' => $eventsWarning,
            'alert_count' => $eventsAlert,
            'execution_time' => $formattedTime,
            'total_count' => $count,
        ]);
    }

    private function formatExecutionTime(float $milliseconds): string
    {
        if ($milliseconds < 1) {
            return number_format($milliseconds * 1000, 2).'μs'; // microseconds
        } elseif ($milliseconds < 1000) {
            return number_format($milliseconds, 2).'ms'; // milliseconds
        } else {
            return number_format($milliseconds / 1000, 2).'s'; // seconds
        }
    }
}
