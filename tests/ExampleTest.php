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
}

class Product extends Model 
{
    protected $guarded = [];
    protected $casts = [
        'price' => 'integer',
    ];
}
