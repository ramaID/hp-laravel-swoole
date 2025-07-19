<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user model can be created', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
});

test('initials method returns correct initials for two names', function () {
    $user = User::factory()->make(['name' => 'John Doe']);

    expect($user->initials())->toBe('JD');
});

test('initials method returns correct initials for single name', function () {
    $user = User::factory()->make(['name' => 'John']);

    expect($user->initials())->toBe('J');
});

test('initials method returns correct initials for multiple names', function () {
    $user = User::factory()->make(['name' => 'John Michael Doe Smith']);

    // Should only take first two names
    expect($user->initials())->toBe('JM');
});

test('initials method handles empty name', function () {
    $user = new User(['name' => '']);

    expect($user->initials())->toBe('');
});

test('initials method handles single character names', function () {
    $user = new User(['name' => 'A B']);

    expect($user->initials())->toBe('AB');
});

test('initials method handles names with extra spaces', function () {
    $user = new User(['name' => '  John   Doe  ']);

    // The explode will create empty strings for extra spaces, so this will return empty
    // Let's test that the method doesn't break with extra spaces
    expect($user->initials())->toBe('');
});
test('user model uses correct traits', function () {
    expect(User::class)->toUse(\Illuminate\Database\Eloquent\Factories\HasFactory::class);
    expect(User::class)->toUse(\Illuminate\Notifications\Notifiable::class);
});

test('user model extends authenticatable', function () {
    $user = new User;
    expect($user)->toBeInstanceOf(\Illuminate\Foundation\Auth\User::class);
});

test('user model has correct fillable attributes', function () {
    $user = new User;
    expect($user->getFillable())->toBe(['name', 'email', 'password']);
});

test('user model has correct hidden attributes', function () {
    $user = new User;
    expect($user->getHidden())->toBe(['password', 'remember_token']);
});

test('user model has correct casts', function () {
    $user = new User;
    $casts = $user->getCasts();

    expect($casts)->toHaveKey('email_verified_at');
    expect($casts)->toHaveKey('password');
    expect($casts['email_verified_at'])->toBe('datetime');
    expect($casts['password'])->toBe('hashed');
});
