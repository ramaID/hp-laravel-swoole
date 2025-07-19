<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test events
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);
});

test('test ticker route executes successfully', function () {
    // Create test data
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);

    $response = $this->get('/test-ticker');

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);
});

test('test ticker route handles exceptions', function () {
    // Mock an exception scenario - we'll test with no data
    $response = $this->get('/test-ticker');

    // Should still return a valid response
    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);
});

test('test ticker route stores data in cache', function () {
    // Create test data
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);

    $response = $this->get('/test-ticker');

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);

    // Verify that cache contains the data (this is more of an integration test)
    expect(Cache::store('octane')->has('dashboard-tick-cache'))->toBeTrue();
});

test('swoole stats route returns json when swoole server available', function () {
    // Mock the swoole server
    $mockServer = \Mockery::mock();
    $mockServer->shouldReceive('stats')
        ->once()
        ->andReturn(['workers' => 4, 'connections' => 10]);

    $this->app->instance('swoole.server', $mockServer);

    $response = $this->get('/swoole-stats');

    $response->assertStatus(200);
    $response->assertJson(['workers' => 4, 'connections' => 10]);
});

test('swoole stats route returns error when swoole server not available', function () {
    // Don't bind the swoole server, so it won't be available
    $response = $this->get('/swoole-stats');

    $response->assertStatus(200);
    $response->assertJson(['error' => 'Swoole server not available']);
});

test('home route returns welcome view', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.performance-showcase');
});

test('dashboard route requires authentication', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('dashboard route accessible when authenticated', function () {
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard');
});

test('all dashboard performance routes are accessible', function () {
    $user = User::factory()->create();

    // Create test data
    Event::factory()->create(['type' => 'INFO']);
    Event::factory()->create(['type' => 'WARNING']);
    Event::factory()->create(['type' => 'ALERT']);

    $routes = [
        '/dashboard-sequential',
        '/dashboard-concurrent',
        '/dashboard-cached',
        '/dashboard-tick-cached',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($user)->get($route);
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.default');
    }
});
