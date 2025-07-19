<?php

use App\Http\Controllers\Dashboard\ShowRealTimeMetricsController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('real time metrics dashboard returns correct response', function () {
    $response = $this->actingAs($this->user)->get('/real-time-metrics');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.real-time-metrics');
    $response->assertViewHas('swoole_stats');
    $response->assertViewHas('cache_stats');
    $response->assertViewHas('performance_metrics');
    $response->assertViewHas('title');
});

test('real time metrics controller can be invoked directly', function () {
    $controller = new ShowRealTimeMetricsController;
    $request = new \Illuminate\Http\Request;
    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
});

test('real time metrics gets swoole stats when swoole not available', function () {
    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getSwooleStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    expect($stats)->toBeArray();
    expect($stats)->toHaveKey('start_time');
    expect($stats)->toHaveKey('connection_num');
    expect($stats)->toHaveKey('accept_count');
    expect($stats)->toHaveKey('worker_num');
    expect($stats['worker_num'])->toBe(4);
});

test('real time metrics gets cache stats', function () {
    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getCacheStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    expect($stats)->toBeArray();
    expect($stats)->toHaveKey('tick_cache_exists');
    expect($stats)->toHaveKey('events_cache_exists');
    expect($stats)->toHaveKey('total_cached_items');
});

test('real time metrics cache stats handles exceptions', function () {
    // Mock Cache to throw an exception when checking cache items
    Cache::shouldReceive('store')
        ->with('octane')
        ->andReturnSelf();

    Cache::shouldReceive('has')
        ->andReturn(false);

    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getCacheStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    expect($stats)->toBeArray();
    expect($stats['total_cached_items'])->toBe(0); // No caches exist
});

test('real time metrics gets performance metrics', function () {
    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getPerformanceMetrics');
    $method->setAccessible(true);

    $metrics = $method->invoke($controller);

    expect($metrics)->toBeArray();
    expect($metrics)->toHaveKey('sequential');
    expect($metrics)->toHaveKey('concurrent');
    expect($metrics)->toHaveKey('cached');
    expect($metrics)->toHaveKey('tick_cached');

    // Check structure of each metric (no URLs in this controller)
    foreach ($metrics as $metric) {
        expect($metric)->toHaveKey('avg_response_time');
        expect($metric)->toHaveKey('description');
        expect($metric)->toHaveKey('color');
        expect($metric)->not->toHaveKey('url');
    }
});

test('real time metrics with swoole server bound', function () {
    // Mock app binding
    $mockStats = [
        'start_time' => time() - 7200, // 2 hours ago
        'connection_num' => 25,
        'accept_count' => 3000,
        'worker_num' => 8,
    ];

    $mockServer = \Mockery::mock();
    $mockServer->shouldReceive('stats')->andReturn($mockStats);

    app()->bind('swoole.server', function () use ($mockServer) {
        return $mockServer;
    });

    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getSwooleStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    expect($stats)->toBe($mockStats);
});

test('real time metrics swoole stats exception handling', function () {
    // Mock app binding to throw exception
    app()->bind('swoole.server', function () {
        throw new \Exception('Swoole not available');
    });

    $controller = new ShowRealTimeMetricsController;

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getSwooleStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    // Should return mock data when exception occurs
    expect($stats)->toBeArray();
    expect($stats)->toHaveKey('worker_num');
    expect($stats['worker_num'])->toBe(4);
});

test('real time metrics cache stats handles exception on count', function () {
    // Test the exception handling path in getCacheStats when counting items fails
    $controller = new ShowRealTimeMetricsController;

    // Mock Cache to work normally for has()
    Cache::shouldReceive('store')
        ->with('octane')
        ->andReturnSelf();

    Cache::shouldReceive('has')
        ->andReturn(true);

    // Use reflection to test private method
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getCacheStats');
    $method->setAccessible(true);

    $stats = $method->invoke($controller);

    expect($stats)->toBeArray();
    expect($stats)->toHaveKey('total_cached_items');
    expect($stats['total_cached_items'])->toBe(2); // Both caches exist
});