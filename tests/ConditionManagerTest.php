<?php

use Gloudemans\Shoppingcart\Cart;
use Soap\LaravelCartConditions\CartCondition;

// Bind a new Cart instance in the container before each test.
beforeEach(function () {
    // Bind a minimal event dispatcher.
    if (! app()->bound('events')) {
        app()->singleton('events', function ($app) {
            return new Dispatcher($app);
        });
    }

    // Bind a minimal session store.
    if (! app()->bound('session.store')) {
        app()->singleton('session.store', function () {
            return new ArrayStore('test');
        });
    }

    // Bind a SessionManager to the container.
    if (! app()->bound('session')) {
        app()->singleton('session', function ($app) {
            return new SessionManager($app);
        });
    }

    // Now create the Cart instance with the required dependencies.
    $session = app('session');     // Instance of SessionManager
    $dispatcher = app('events');   // Instance of Dispatcher
    app()->instance('cart', new Cart($session, $dispatcher, 'default'));
});

it('applies percentage discount on subtotal using cart macros', function () {
    // Create a new cart instance and initialize properties.
    $cart = app('cart');
    $cart->subtotal = 100;
    $cart->total = 100; // Property for demonstration.

    // Use the withConditions macro to attach a 10% discount on the subtotal.
    $cart->withConditions([
        new CartCondition(
            name: '10% Off',
            target: 'subtotal',
            type: 'discount',
            operator: '%',
            value: 10
        ),
    ]);

    // The discount should reduce the subtotal from 100 to 90.
    expect($cart->applyConditions('subtotal'))->toEqual(90);
});

// Test that the "addCondition" and "totalWithConditions" macros work together,
// by attaching a surcharge and checking the computed total.
it('applies surcharge on total using cart macros', function () {
    // Create a new cart instance and initialize properties.
    $cart = app('cart');
    $cart->subtotal = 100;
    $cart->total = 100;

    // Use the addCondition macro to add a $5 surcharge to the total.
    $cart->addCondition(new CartCondition(
        name: '5 USD Fee',
        target: 'total',
        type: 'surcharge',
        operator: '+',
        value: 5
    ));

    // The total should be adjusted from 100 to 105.
    expect($cart->totalWithConditions())->toEqual(105);
});
