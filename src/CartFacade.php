<?php

namespace Ganyicz\Cart;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ganyicz\Cart\Cart
 */
class CartFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eloquent-cart';
    }
}
