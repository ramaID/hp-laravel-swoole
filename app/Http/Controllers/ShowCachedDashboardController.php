<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Facades\Octane;

class ShowCachedDashboardController extends Controller
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
                        fn () => Event::query()->ofType('INFO')->get(),
                        fn () => Event::query()->ofType('WARNING')->get(),
                        fn () => Event::query()->ofType('ALERT')->get(),
                    ]);
                }
            );
        } catch (Exception $e) {
            // Handle potential exceptions from the cache or concurrent tasks
            return 'Error: '.$e->getMessage();
        }

        $time = (hrtime(true) - $time) / 1_000_000;

        return "Fetched from cache in {$time}ms. Count: {$count}, Info: {$eventsInfo->count()}, Warning: {$eventsWarning->count()}, Alert: {$eventsAlert->count()}";
    }
}
