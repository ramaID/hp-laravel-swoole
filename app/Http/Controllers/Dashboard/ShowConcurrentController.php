<?php

// app/Http/Controllers/ShowConcurrentDashboardController.php

namespace App\Http\Controllers\Dashboard;

use App\Models\Event;
use Illuminate\Http\Request;
use Laravel\Octane\Exceptions\TaskTimeoutException;
use Laravel\Octane\Facades\Octane;

class ShowConcurrentController extends \App\Http\Controllers\Controller
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
            return response('Error: A task timed out.', 500);
        }

        $time = (hrtime(true) - $time) / 1_000_000; // time in ms

        // Format execution time for display
        $executionTime = $time < 1000 ? number_format($time, 2).' ms' : number_format($time / 1000, 2).' s';

        return view('dashboard.default', [
            'info_count' => $eventsInfo->count(),
            'warning_count' => $eventsWarning->count(),
            'alert_count' => $eventsAlert->count(),
            'execution_time' => $executionTime,
            'total_count' => $count,
        ]);
    }
}
