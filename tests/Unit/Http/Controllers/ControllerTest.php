<?php

use App\Http\Controllers\Controller;

test('controller can be instantiated', function () {
    // Since Controller is abstract, we need to create a concrete implementation
    $controller = new class extends Controller
    {
        public function testMethod()
        {
            return 'test';
        }
    };

    expect($controller)->toBeInstanceOf(Controller::class);
});

test('controller does not extend a parent class', function () {
    $reflection = new \ReflectionClass(Controller::class);
    $parentClass = $reflection->getParentClass();

    expect($parentClass)->toBeFalse();
});

test('controller is abstract', function () {
    $reflection = new \ReflectionClass(Controller::class);
    expect($reflection->isAbstract())->toBeTrue();
});
