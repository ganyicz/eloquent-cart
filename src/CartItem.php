<?php

namespace Ganyicz\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CartItem
{
    public int $quantity = 1;

    public function __construct(public Model $model)
    {
    }

    public function total()
    {
        return $this->quantity * $this->unit_price;
    }

    public function unitPrice()
    {
        return $this->model->price;
    }

    private function hasAccessor($attribute)
    {
        return method_exists($this, Str::studly($attribute));
    }

    private function callAccessor($attribute)
    {
        $accessor = Str::studly($attribute);
        
        return $this->$accessor();
    }

    public function __get($attribute)
    {
        if ($this->hasAccessor($attribute)) {
            return $this->callAccessor($attribute);
        }

        return $this->$attribute;
    }
}
