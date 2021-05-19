<?php

namespace Ganyicz\Cart;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ganyicz\Cart\CartManager
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
