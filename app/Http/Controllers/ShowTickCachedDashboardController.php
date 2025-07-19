<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShowTickCachedDashboardController extends Controller
{
    /**
     * Handle the incoming request using tick-warmed cache.
     */
    public function __invoke(Request $request)
    {
        $time = hrtime(true);

        $result = Cache::store('octane')->get('dashboard-tick-cache');

        $time = (hrtime(true) - $time) / 1_000_000;

        // Format time for better readability
        $formattedTime = $this->formatExecutionTime($time);

        // Debug information
        if (! $result) {
            return 'Cache not yet warmed. Please warm it by visiting: /test-ticker';
        }

        return "Fetched from tick cache in {$formattedTime}. " .
               "Total Count: {$result['count']}, " .
               "Info: " . ($result['eventsInfo'] ?? 0) . ", " .
               "Warning: " . ($result['eventsWarning'] ?? 0) . ", " .
               "Alert: " . ($result['eventsAlert'] ?? 0) . ". " .
               "Last updated: " . ($result['last_updated'] ?? 'Unknown');
    }

    private function formatExecutionTime(float $milliseconds): string
    {
        if ($milliseconds < 1) {
            return number_format($milliseconds * 1000, 2) . 'μs'; // microseconds
        } elseif ($milliseconds < 1000) {
            return number_format($milliseconds, 2) . 'ms'; // milliseconds
        } else {
            return number_format($milliseconds / 1000, 2) . 's'; // seconds
        }
    }
}
