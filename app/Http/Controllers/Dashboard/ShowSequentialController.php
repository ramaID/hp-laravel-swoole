<?php

// app/Http/Controllers/ShowSequentialDashboardController.php

namespace App\Http\Controllers\Dashboard;

use App\Models\Event;

class ShowSequentialController extends \App\Http\Controllers\Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $time = hrtime(true);

        $count = Event::query()->count();
        $eventsInfo = Event::query()->ofType('INFO')->get();
        $eventsWarning = Event::query()->ofType('WARNING')->get();
        $eventsAlert = Event::query()->ofType('ALERT')->get();

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
