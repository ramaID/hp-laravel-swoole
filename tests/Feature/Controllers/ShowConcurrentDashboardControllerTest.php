<?php

use App\Http\Controllers\Dashboard\ShowConcurrentController;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create test events
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);
});

test('concurrent dashboard returns response with timing', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-concurrent');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('info_count', 5);
    $response->assertViewHas('execution_time');
});

test('concurrent dashboard controller can be invoked directly', function () {
    $controller = new ShowConcurrentController;
    $request = new \Illuminate\Http\Request;
    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
});

test('concurrent dashboard processes correct data', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-concurrent');

    $response->assertStatus(200);
    $response->assertViewHas('info_count', 5);
    $response->assertViewHas('warning_count', 5);
    $response->assertViewHas('alert_count', 5);
});

test('concurrent dashboard timing is formatted correctly', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-concurrent');

    $executionTime = $response->viewData('execution_time');
    expect($executionTime)->toMatch('/^\d+(\.\d+)?\s(ms|s)$/');
});

test('concurrent dashboard handles task timeout exception', function () {
    // Mock Octane to throw TaskTimeoutException
    \Laravel\Octane\Facades\Octane::shouldReceive('concurrently')
        ->once()
        ->andThrow(new \Laravel\Octane\Exceptions\TaskTimeoutException('Task timed out'));

    $controller = new ShowConcurrentController;
    $request = new \Illuminate\Http\Request;

    $response = $controller($request);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Response::class);
    expect($response->getStatusCode())->toBe(500);
    expect($response->getContent())->toBe('Error: A task timed out.');
});
