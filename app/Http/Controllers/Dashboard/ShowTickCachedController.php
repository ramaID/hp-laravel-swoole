<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShowTickCachedController extends \App\Http\Controllers\Controller
{
    /**
     * Handle the incoming request using tick-warmed cache.
     */
    public function __invoke(Request $request)
    {
        $title = 'Dashboard - Tick Cache';
        $time = hrtime(true);
        $result = Cache::store('octane')->get('dashboard-tick-cache');

        $time = (hrtime(true) - $time) / 1_000_000;

        // Format time for better readability
        $formattedTime = $this->formatExecutionTime($time);

        // Debug information
        if (! $result) {
            return view('dashboard.default', [
                'info_count' => 0,
                'warning_count' => 0,
                'alert_count' => 0,
                'execution_time' => $formattedTime,
                'total_count' => 0,
                'cache_status' => 'Cache not yet warmed. Please warm it by visiting: /test-ticker',
                'title' => $title,
            ]);
        }

        return view('dashboard.default', [
            'info_count' => $result['eventsInfo'] ?? 0,
            'warning_count' => $result['eventsWarning'] ?? 0,
            'alert_count' => $result['eventsAlert'] ?? 0,
            'execution_time' => $formattedTime,
            'total_count' => $result['count'] ?? 0,
            'cache_status' => 'Data loaded from tick cache',
            'title' => $title,
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
