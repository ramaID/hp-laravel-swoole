<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Facades\Octane;

class WarmDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm the dashboard cache with concurrent queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Warming dashboard cache...');

        try {
            $result = Octane::concurrently([
                'count' => fn () => Event::query()->count(),
                'eventsInfo' => fn () => Event::query()->ofType('INFO')->get(),
                'eventsWarning' => fn () => Event::query()->ofType('WARNING')->get(),
                'eventsAlert' => fn () => Event::query()->ofType('ALERT')->get(),
            ]);

            Cache::store('octane')->put('dashboard-tick-cache', $result, 300); // 5 minute cache

            $this->info('Dashboard cache warmed successfully!');
            $this->line('Cache contents:');
            $this->line('- Total events: '.$result['count']);
            $this->line('- Info events: '.$result['eventsInfo']->count());
            $this->line('- Warning events: '.$result['eventsWarning']->count());
            $this->line('- Alert events: '.$result['eventsAlert']->count());

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to warm cache: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
