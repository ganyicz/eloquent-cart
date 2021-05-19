<?php

namespace Ganyicz\Cart;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CartManager
{
    private Collection $items;

    public function __construct()
    {
        $this->items = collect(session('cart.items'));

        $this->refreshModels();
    }

    public function items()
    {
        return $this->items;
    }

    public function total()
    {
        return $this->items->sum(
            fn ($item) =>
            $item->model->price * $item->quantity
        );
    }
    
    public function add(Model $model, int $quantity = 1)
    {
        if ($existingItem = $this->items->first(fn ($item) => $item->model->is($model))) {
            $existingItem->quantity += $quantity;
        } else {
            $this->items->add(tap(
                new CartItem($model),
                fn ($item) =>
                $item->quantity = $quantity
            ));
        }

        $this->update();
    }

    public function update()
    {
        session()->put('cart', [
            'items' => $this->items,
        ]);
    }

    private function refreshModels()
    {
        $freshModels = EloquentCollection::make($this->items->pluck('model'))->fresh();

        $this->items = $this->items
            ->filter(function ($item) use ($freshModels) {
                return $freshModels->contains($item->model);
            })
            ->each(function ($item) use ($freshModels) {
                $item->model = $freshModels->find($item->model);
            });
    }
}
