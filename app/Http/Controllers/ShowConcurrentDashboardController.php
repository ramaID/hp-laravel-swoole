<?php

// app/Http/Controllers/ShowConcurrentDashboardController.php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Laravel\Octane\Exceptions\TaskTimeoutException;
use Laravel\Octane\Facades\Octane;

class ShowConcurrentDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $time = hrtime(true);

        try {
            [$count, $eventsInfo, $eventsWarning, $eventsAlert] = Octane::concurrently([
                fn () => Event::query()->count(),
                fn () => Event::query()->ofType('INFO')->get(),
                fn () => Event::query()->ofType('WARNING')->get(),
                fn () => Event::query()->ofType('ALERT')->get(),
            ]);
        } catch (TaskTimeoutException $e) {
            return 'Error: A task timed out.';
        }

        $time = (hrtime(true) - $time) / 1_000_000; // time in ms

        // Total time will be the time of the SLOWEST query, ~1 second
        return "Fetched concurrently in {$time}ms";
    }
}
