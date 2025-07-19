<?php

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

test('sequential dashboard returns response with timing', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-sequential');

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.default');
    $response->assertViewHas('execution_time');
});

test('sequential dashboard controller can be invoked', function () {
    $controller = new \App\Http\Controllers\Dashboard\ShowSequentialController;

    $response = $controller();

    expect($response)->toBeInstanceOf(\Illuminate\View\View::class);
});

test('sequential dashboard processes queries in sequence', function () {
    $response = $this->actingAs($this->user)->get('/dashboard-sequential');

    $response->assertStatus(200);
    $response->assertViewHas('info_count', 1);
    $response->assertViewHas('warning_count', 1);
    $response->assertViewHas('alert_count', 1);
    $response->assertViewHas('execution_time');
});
