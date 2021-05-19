<?php

namespace Ganyicz\Cart\Tests;

use Ganyicz\Cart\Cart;
use Ganyicz\Cart\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function model_can_be_added()
    {
        $product = Product::create(['price' => 100_00]);
        
        Cart::add($product);

        $this->assertNotEmpty(session('cart'));
        $this->assertCount(1, session('cart.items'));
    }

    /** @test */
    public function quantity_can_be_specified()
    {
        $product = Product::create(['price' => 100_00]);
        
        Cart::add($product, 2);

        $this->assertEquals(2, session('cart.items')[0]->quantity);
    }

    /** @test */
    public function total_is_calculated()
    {
        $product = Product::create(['price' => 100_00]);
        
        Cart::add($product, 2);

        $this->assertEquals(200_00, Cart::total());
    }

    /** @test */
    public function adding_existing_model_increments_quantity()
    {
        $product = Product::create(['price' => 100_00]);
        
        Cart::add($product);
        Cart::add($product);

        $this->assertEquals(2, session('cart.items')[0]->quantity);
    }

    /** @test */
    public function fetches_fresh_instance_of_models()
    {
        $product = Product::create(['price' => 100_00]);

        session()->put('cart.items', [
            new CartItem(clone $product),
        ]);

        $product->update(['price' => 200_00]);

        $this->assertEquals(200_00, Cart::total());
    }

    /** @test */
    public function filters_out_non_existent_models()
    {
        $product = Product::create(['price' => 100_00]);

        session()->put('cart.items', [
            new CartItem(clone $product),
        ]);

        $product->delete();

        $this->assertEquals(0, Cart::total());
    }

    /** @test */
    public function cart_item_calculates_total()
    {
        $product = Product::create(['price' => 100_00]);

        Cart::add($product, 2);

        $this->assertEquals(200_00, Cart::items()->first()->total());
    }

    /** @test */
    public function cart_item_total_can_be_accessed_as_a_property()
    {
        $product = Product::create(['price' => 100_00]);

        Cart::add($product, 2);

        $this->assertEquals(200_00, Cart::items()->first()->total);
    }
}

class Product extends Model
{
    protected $guarded = [];
    protected $casts = [
        'price' => 'integer',
    ];
}
