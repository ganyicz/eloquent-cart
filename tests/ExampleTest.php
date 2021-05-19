<?php

namespace Ganyicz\Cart\Tests;

use Ganyicz\Cart\Cart;
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

        $this->assertEquals(2, session('cart.items')[0]['quantity']);
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

        $this->assertEquals(2, session('cart.items')[0]['quantity']);
    }
}

class Product extends Model 
{
    protected $guarded = [];
    protected $casts = [
        'price' => 'integer',
    ];
}
