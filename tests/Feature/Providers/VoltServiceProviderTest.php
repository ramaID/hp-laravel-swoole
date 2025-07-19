<?php

use App\Providers\VoltServiceProvider;
use Livewire\Volt\Volt;

test('volt service provider can be instantiated', function () {
    $provider = new VoltServiceProvider(app());
    expect($provider)->toBeInstanceOf(VoltServiceProvider::class);
});

test('volt service provider register method does nothing', function () {
    $provider = new VoltServiceProvider(app());

    // Should not throw any exceptions
    $provider->register();

    expect(true)->toBeTrue();
});

test('volt service provider boot method mounts directories', function () {
    // Mock Volt facade
    Volt::spy();

    $provider = new VoltServiceProvider(app());
    $provider->boot();

    // Verify that Volt::mount was called
    Volt::shouldHaveReceived('mount')
        ->once()
        ->with(\Mockery::type('array'));
});

test('volt service provider mounts correct directories', function () {
    // Mock Volt facade to capture the mount call
    $mountedPaths = null;

    Volt::shouldReceive('mount')
        ->once()
        ->with(\Mockery::type('array'))
        ->andReturnUsing(function ($paths) use (&$mountedPaths) {
            $mountedPaths = $paths;

            return null;
        });

    $provider = new VoltServiceProvider(app());
    $provider->boot();

    expect($mountedPaths)->toBeArray();
    expect($mountedPaths)->toContain(resource_path('views/livewire'));
    expect($mountedPaths)->toContain(resource_path('views/pages'));
});
