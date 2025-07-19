<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Clear cache before each test
    Cache::store('octane')->forget('dashboard-tick-cache');
});

test('tick cached dashboard returns cache not warmed message when cache is empty', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-tick-cached');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('cache_status', 'Cache not yet warmed. Please warm it by visiting: /test-ticker');
});

test('tick cached dashboard returns cached data when cache exists', function () {
    // Pre-populate cache
    $cacheData = [
        'count' => 5,
        'eventsInfo' => 2,
        'eventsWarning' => 2,
        'eventsAlert' => 1,
    ];

    Cache::store('octane')->put('dashboard-tick-cache', $cacheData, 300);

    $response = $this->actingAs($this->user)->get('/dashboard-tick-cached');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('total_count', 5);
    $response->assertViewHas('info_count', 2);
    $response->assertViewHas('warning_count', 2);
    $response->assertViewHas('alert_count', 1);
});

test('tick cached dashboard controller can be invoked directly', function () {
    $controller = new \App\Http\Controllers\Dashboard\ShowTickCachedController;
    $request = request();

    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
});

test('tick cached dashboard formats execution time correctly', function () {
    // Use reflection to test the private formatExecutionTime method
    $controller = new \App\Http\Controllers\Dashboard\ShowTickCachedController;
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('formatExecutionTime');
    $method->setAccessible(true);

    // Test microseconds
    expect($method->invoke($controller, 0.5))->toBe('500.00μs');

    // Test milliseconds
    expect($method->invoke($controller, 5.5))->toBe('5.50ms');

    // Test seconds
    expect($method->invoke($controller, 1500.0))->toBe('1.50s');
});

test('tick cached dashboard shows proper timing format', function () {
    // Pre-populate cache to test timing display
    $cacheData = [
        'count' => 3,
        'eventsInfo' => 1,
        'eventsWarning' => 1,
        'eventsAlert' => 1,
    ];

    Cache::store('octane')->put('dashboard-tick-cache', $cacheData, 300);

    $response = $this->actingAs($this->user)->get('/dashboard-tick-cached');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('execution_time');
});

test('tick cached dashboard handles missing cache data gracefully', function () {
    // Pre-populate cache with partial data
    $cacheData = [
        'count' => 3,
        // Missing eventsInfo, eventsWarning, eventsAlert
    ];

    Cache::store('octane')->put('dashboard-tick-cache', $cacheData, 300);

    $response = $this->actingAs($this->user)->get('/dashboard-tick-cached');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('total_count', 3);
    $response->assertViewHas('info_count', 0);
    $response->assertViewHas('warning_count', 0);
    $response->assertViewHas('alert_count', 0);
});
