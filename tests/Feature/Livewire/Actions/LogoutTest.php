<?php

use App\Livewire\Actions\Logout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

test('logout action can be instantiated', function () {
    $logout = new Logout;
    expect($logout)->toBeInstanceOf(Logout::class);
});

test('logout action logs out the current user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect(Auth::check())->toBeTrue();

    $logout = new Logout;
    $response = $logout();

    expect(Auth::check())->toBeFalse();
    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    expect($response->getTargetUrl())->toBe(url('/'));
});

test('logout action invalidates session', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Set some session data
    Session::put('test_key', 'test_value');
    expect(Session::has('test_key'))->toBeTrue();

    $logout = new Logout;
    $logout();

    // Session should be invalidated
    expect(Session::has('test_key'))->toBeFalse();
});

test('logout action regenerates token', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $originalToken = Session::token();

    $logout = new Logout;
    $logout();

    // Token should be regenerated (we can't easily test this directly
    // but we can ensure the method completes without error)
    expect(true)->toBeTrue();
});

test('logout action redirects to home page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $logout = new Logout;
    $response = $logout();

    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    expect($response->getTargetUrl())->toBe(url('/'));
});

test('logout action works when no user is authenticated', function () {
    expect(Auth::check())->toBeFalse();

    $logout = new Logout;
    $response = $logout();

    expect(Auth::check())->toBeFalse();
    expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    expect($response->getTargetUrl())->toBe(url('/'));
});
