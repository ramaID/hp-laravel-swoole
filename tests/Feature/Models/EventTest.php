<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('event model can be created', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'type' => 'INFO',
        'date' => now(),
    ]);

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->type)->toBe('INFO');
    expect($event->date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($event->user_id)->toBe($user->id);
});

test('ofType scope filters events by type', function () {
    $user = User::factory()->create();

    // Create events of different types
    Event::factory()->create(['type' => 'INFO', 'user_id' => $user->id]);
    Event::factory()->create(['type' => 'WARNING', 'user_id' => $user->id]);
    Event::factory()->create(['type' => 'ALERT', 'user_id' => $user->id]);
    Event::factory()->create(['type' => 'INFO', 'user_id' => $user->id]);

    // Test the scope - we'll check the query structure
    $query = Event::query()->ofType('INFO');

    // Check that the query has the correct where clause
    $sql = $query->toSql();
    expect($sql)->toContain('where "type" = ?');
    expect($sql)->toContain('order by "date" desc');
    expect($sql)->toContain('limit 5');
});

test('ofType scope orders by date descending', function () {
    $user = User::factory()->create();

    // Create events with different dates
    Event::factory()->create([
        'type' => 'INFO',
        'user_id' => $user->id,
        'date' => now()->subDays(2),
    ]);

    Event::factory()->create([
        'type' => 'INFO',
        'user_id' => $user->id,
        'date' => now()->subDay(),
    ]);

    Event::factory()->create([
        'type' => 'INFO',
        'user_id' => $user->id,
        'date' => now(),
    ]);

    // Test the query structure
    $query = Event::query()->ofType('INFO');
    expect($query->toSql())->toContain('order by "date" desc');
});

test('ofType scope limits results to 5', function () {
    $user = User::factory()->create();

    // Create more than 5 events
    Event::factory()->count(10)->create(['type' => 'INFO', 'user_id' => $user->id]);

    // Test that the query has limit 5
    $query = Event::query()->ofType('INFO');
    expect($query->toSql())->toContain('limit 5');
});

test('event uses HasFactory trait', function () {
    expect(Event::class)->toUse(\Illuminate\Database\Eloquent\Factories\HasFactory::class);
});

test('event extends Model', function () {
    $event = new Event;
    expect($event)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
});

test('event belongs to user', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    expect($event->user)->toBeInstanceOf(User::class);
    expect($event->user->id)->toBe($user->id);
});

test('event has required attributes', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'user_id' => $user->id,
        'type' => 'WARNING',
        'description' => 'Test description',
        'value' => 42,
    ]);

    expect($event->type)->toBe('WARNING');
    expect($event->description)->toBe('Test description');
    expect($event->value)->toBe(42);
    expect($event->user_id)->toBe($user->id);
});
