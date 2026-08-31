<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('clears only the current customers cart', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();
    $product = Product::factory()->create();
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);
    $otherBuyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($buyer)->delete(route('cart.clear'))
        ->assertRedirect(route('cart.index'));

    expect($buyer->cartItems()->count())->toBe(0)
        ->and($otherBuyer->cartItems()->count())->toBe(1);
});

it('allows pickup checkout without a street address', function () {
    Notification::fake();
    $buyer = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 80,
        'compare_price' => 100,
        'stock' => 3,
    ]);
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($buyer)->post(route('checkout.store'), [
        'name' => 'Pickup Buyer',
        'city' => 'Ashgabat',
        'phone' => '+99364005374',
        'delivery_method' => 'pickup',
        'payment_method' => 'cash',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'user_id' => $buyer->id,
        'address' => 'Самовывоз',
        'subtotal' => 100,
        'discount' => 20,
        'total' => 80,
    ]);
});

it('previews a valid promo code without creating an order', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 3]);
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);
    PromoCode::create([
        'code' => 'SAVE10',
        'name' => 'Ten percent',
        'type' => 'percent',
        'value' => 10,
        'minimum_amount' => 0,
        'per_user_limit' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($buyer)->postJson(route('checkout.promo'), [
        'promo_code' => 'save10',
    ])->assertOk()
        ->assertJsonPath('code', 'SAVE10')
        ->assertJsonPath('discount', 10)
        ->assertJsonPath('total', 90);

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('promo_code_usages', 0);
});

it('filters the order list and renders a detailed order page', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create();
    $pending = Order::factory()->for($buyer)->create(['number' => 'AK-PENDING']);
    $delivered = Order::factory()->for($buyer)->create([
        'number' => 'AK-DELIVERED',
        'status' => 'delivered',
        'subtotal' => 50,
        'total' => 50,
    ]);
    OrderItem::create([
        'order_id' => $delivered->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 50,
        'quantity' => 1,
        'status' => 'delivered',
    ]);

    $this->actingAs($buyer)->get(route('orders.index', ['status' => 'delivered']))
        ->assertOk()
        ->assertSee('AK-DELIVERED')
        ->assertDontSee('AK-PENDING');

    $this->actingAs($buyer)->get(route('orders.show', $delivered))
        ->assertOk()
        ->assertSee('AK-DELIVERED')
        ->assertSee($product->name);

    expect($pending->exists)->toBeTrue();
});

it('renders discount and new-product labels as attached ribbons', function () {
    $product = Product::factory()->create([
        'price' => 50,
        'compare_price' => 100,
        'created_at' => now(),
    ]);

    $this->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertSee('product-ribbon--discount', false)
        ->assertSee('product-ribbon--new', false);
});
