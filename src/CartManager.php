<?php

namespace Ganyicz\Cart;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

class CartManager
{
    public function items()
    {
        return session()->get('cart.items', collect());
    }

    public function context()
    {
        return session()->get('cart.context', []);
    }

    public function empty()
    {
        return $this->items()->isEmpty();
    }

    public function subtotal()
    {
        return $this->items()->sum->total();
    }

    public function total()
    {
        return $this->items()->sum->total();
    }

    /**
     * @param Model|string $id
     * @return CartItem|null
     */
    public function find($id): ?CartItem
    {
        return $this->items()->first(fn ($item) => 
            $id instanceof Model
                ? $item->model->is($id)
                : $item->id === (string) $id
        );
    }
    
    public function add(Model $model, int $quantity = 1)
    {
        if ($existingItem = $this->find($model)) {
            $existingItem->increment($quantity);
            return;
        }
        
        session()->put('cart.items', $this->items()->add(
            tap(new CartItem($model), fn ($item) => 
                $item->quantity = $quantity
            )
        ));
    }

    public function destroy()
    {
        session()->forget('cart');
    }

    public function has($key)
    {
        return session()->has("cart.context.{$key}");
    }

    public function get($key)
    {
        return session()->get("cart.context.{$key}");
    }

    public function remember($key, $value)
    {
        session()->put("cart.context.{$key}", $value);
    }

    public function forget($key)
    {
        session()->forget("cart.context.{$key}");
    }

    public function replace($id, $newInstance)
    {
        if (session()->has("cart.context.{$id}")) {
            session()->put("cart.context.{$id}", $newInstance);
            return;
        }
        
        session()->put('cart.items', $this->items()->map(fn ($item) =>
            $item->id === $id ? $newInstance : $item
        ));
    }

    public function remove($id)
    {
        session()->put('cart.items', $this->items()->reject(fn ($item) => 
            $item->id === $id
        ));
    }

    /**
     * Fetch fresh model attributes from database and remove non-existent ones
     *
     * @return void
     */
    public function refresh()
    {
        $freshModels = EloquentCollection::make($this->items()->pluck('model'))->fresh();

        session()->put('cart.items', 
            $this->items()
                ->filter(fn ($item) => $freshModels->contains($item->model))
                ->each(fn ($item) =>
                    $item->model = $freshModels->find($item->model)
                )
        );
    }
}
