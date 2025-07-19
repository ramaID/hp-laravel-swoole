<?php

use App\Console\Commands\WarmDashboardCache;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Facades\Octane;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test events
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);
});

test('warm dashboard cache command executes successfully', function () {
    $this->artisan('dashboard:warm-cache')
        ->expectsOutput('Warming dashboard cache...')
        ->expectsOutput('Dashboard cache warmed successfully!')
        ->expectsOutput('Cache contents:')
        ->assertExitCode(0);
});

test('warm dashboard cache command handles exceptions', function () {
    // Mock Octane to throw an exception
    Octane::shouldReceive('concurrently')
        ->once()
        ->andThrow(new Exception('Test exception'));

    $this->artisan('dashboard:warm-cache')
        ->expectsOutput('Warming dashboard cache...')
        ->expectsOutput('Failed to warm cache: Test exception')
        ->assertExitCode(1);
});

test('warm dashboard cache command stores data in cache', function () {
    $this->artisan('dashboard:warm-cache');

    // Verify cache was set
    expect(Cache::store('octane')->has('dashboard-tick-cache'))->toBeTrue();

    $cached = Cache::store('octane')->get('dashboard-tick-cache');
    expect($cached)->toHaveKey('count');
    expect($cached)->toHaveKey('eventsInfo');
    expect($cached)->toHaveKey('eventsWarning');
    expect($cached)->toHaveKey('eventsAlert');
});

test('warm dashboard cache command can be instantiated', function () {
    $command = new WarmDashboardCache;

    expect($command->getDescription())->toBe('Warm the dashboard cache with concurrent queries');
    expect($command->getName())->toBe('dashboard:warm-cache');
});
