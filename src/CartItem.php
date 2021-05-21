<?php

namespace Ganyicz\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CartItem
{
    /**
     * Unique id generated when model is added
     */
    public string $id;

    /**
     * The item's quantity
     */
    public int $quantity = 1;

    /**
     * Indicates if the item should be removed from the cart on the next save
     */
    public bool $removed = false;

    public function __construct(public Model $model)
    {
        $this->id = Str::uuid();
    }

    public function total()
    {
        return $this->quantity * $this->unit_price;
    }

    public function unitPrice()
    {
        return $this->model->price;
    }

    /**
     * Update the item's quantity and save the cart instance
     * 
     * @return void
     */
    public function quantity(int $quantity)
    {
        $this->quantity = $quantity;

        $this->save();
    }

    public function remove()
    {
        $this->removed = true;

        $this->save();
    }

    public function save()
    {
        Cart::save();
    }

    private function hasAccessor($attribute)
    {
        return in_array(Str::camel($attribute), ['total', 'unitPrice']);
    }

    private function callAccessor($attribute)
    {
        $accessor = Str::camel($attribute);
        
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
