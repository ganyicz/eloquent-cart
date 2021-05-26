<?php

namespace Ganyicz\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use Illuminate\Support\Str;

class CartItem
{
    use SerializesAndRestoresModelIdentifiers;

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
        return $this->model->getPrice() ?? $this->model->price ?? 0;
    }

    public function increment($quantity = 1)
    {
        $this->quantity += $quantity;

        $this->save();
    }

    public function decrement($quantity = 1)
    {
        $this->quantity -= $quantity;

        $this->save();
    }

    public function update(int $quantity)
    {
        $this->quantity = $quantity;

        $this->save();
    }

    public function remove()
    {
        Cart::remove($this->id);
    }

    public function save()
    {
        Cart::replace($this->id, $this);
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

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $this->model = $this->getSerializedPropertyValue($this->model);

        return array_keys(get_object_vars($this));
    }

    /**
     * Restore the model after serialization.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->model = $this->getRestoredPropertyValue($this->model);
    }
}
