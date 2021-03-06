<?php

namespace Ganyicz\Cart\Tests;

use Ganyicz\Cart\Cart;
use Ganyicz\Cart\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        (new ProductTableMigration)->up();
    }

    public function tearDown(): void
    {
        (new ProductTableMigration)->down();

        parent::tearDown();
    }
    
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
    public function filters_out_items_with_quantity_lower_than_one()
    {
        $product = Product::create(['price' => 100_00]);

        Cart::add($product);

        $item = Cart::find($product);

        $item->quantity--;

        Cart::save();

        $this->assertEmpty(Cart::items());
        $this->assertEmpty(session('cart.items'));
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

    /** @test */
    public function cart_item_can_be_removed()
    {
        $product = Product::create(['price' => 100_00]);

        Cart::add($product);

        Cart::items()->first()->remove();

        $this->assertEquals(0, Cart::total());
        $this->assertEmpty(Cart::items());
        $this->assertEmpty(session('cart.items'));
    }
}

class Product extends Model
{
    protected $guarded = [];
    protected $casts = [
        'price' => 'integer',
    ];
}

class ProductTableMigration extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
