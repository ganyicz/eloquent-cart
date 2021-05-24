<?php

namespace Ganyicz\Cart;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection items() 
 * 
 * @see \Ganyicz\Cart\CartManager
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
