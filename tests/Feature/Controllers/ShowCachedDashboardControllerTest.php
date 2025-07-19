<?php

use App\Http\Controllers\Dashboard\ShowCachedController;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create test events
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);
});

test('cached dashboard returns response with timing', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-cached');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('info_count', 33074);
    $response->assertViewHas('execution_time');
});

test('cached dashboard controller can be invoked directly', function () {
    $controller = new ShowCachedController;
    $request = new \Illuminate\Http\Request;
    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
});

test('cached dashboard processes correct data', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-cached');

    $response->assertStatus(200);
    $response->assertViewHas('info_count', 33074);
});

test('cached dashboard timing is formatted correctly', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-cached');

    $executionTime = $response->viewData('execution_time');
    expect($executionTime)->toMatch('/^\d+(\.\d+)?s$/');
});

test('cached dashboard handles cache exceptions', function () {
    // Mock Cache to throw an exception
    Cache::shouldReceive('store')
        ->with('octane')
        ->once()
        ->andThrow(new \Exception('Cache error'));

    $controller = new ShowCachedController;
    $request = new \Illuminate\Http\Request;

    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Response::class);
    expect($response->getStatusCode())->toBe(500);
    expect($response->getContent())->toBe('Error: Cache error');
});

test('cached dashboard uses cache when available', function () {
    // First request should cache the data
    $response1 = $this->actingAs($this->user)->get('/dashboard-cached');
    $response1->assertStatus(200);

    // Second request should use cached data (faster)
    $response2 = $this->actingAs($this->user)->get('/dashboard-cached');
    $response2->assertStatus(200);
    $response2->assertViewIs('dashboard.default');

    // Both should have the same data
    expect($response1->viewData('info_count'))->toBe($response2->viewData('info_count'));
});
