<?php

namespace Soap\LaravelCartConditions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soap\LaravelCartConditions\LaravelCartConditions
 */
class CartConditions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cartconditions';
    }
}
