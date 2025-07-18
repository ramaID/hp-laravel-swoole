<?php

// app/Http/Controllers/ShowSequentialDashboardController.php

namespace App\Http\Controllers;

use App\Models\Event;

class ShowSequentialDashboardController extends Controller
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

        // Total time will be > 3 seconds (3 queries * 1 second sleep)
        return "Fetched sequentially in {$time}ms";
    }
}
