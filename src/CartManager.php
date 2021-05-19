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
        return $this->items->sum(fn ($item) =>
            $item['model']->price * $item['quantity']
        );
    }
    
    public function add(Model $model, int $quantity = 1)
    {
        $existingItemIndex = $this->items->search(fn ($item) => $item['model']->is($model));
        $existingItem = $existingItemIndex === false ? null : $this->items[$existingItemIndex];

        if ($existingItem) {
            $this->items->put($existingItemIndex, [
                'model' => $model,
                'quantity' => $existingItem['quantity'] + $quantity,
            ]);
        } else {
            $this->items->add([
                'model' => $model,
                'quantity' => $quantity,
            ]);
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
                return $freshModels->contains($item['model']);
            })
            ->map(function ($item) use ($freshModels) {
                return [
                    'model' => $freshModels->find($item['model']),
                    'quantity' => $item['quantity'],
                ];
            });
    }
}
