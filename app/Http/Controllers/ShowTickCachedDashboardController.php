<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        // Debug information
        if (! $result) {
            return 'Cache not yet warmed. Please warm it by visiting: /test-ticker';
        }

        return "Fetched from tick cache in {$time}ms. Count: {$result['count']}, Info: {$result['eventsInfo']->count()}, Warning: {$result['eventsWarning']->count()}, Alert: {$result['eventsAlert']->count()}";
    }
}
