<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('lets a customer cancel their own pending order and restores stock', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $order = Order::factory()->create(['user_id' => $buyer->id, 'status' => 'pending']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'status' => 'pending']);

    $this->actingAs($buyer)->patch(route('orders.cancel', $order))->assertRedirect();

    expect($order->fresh()->status)->toBe('cancelled');
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
});

it('does not let a customer cancel an order that already shipped', function () {
    $buyer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $buyer->id, 'status' => 'shipped']);

    $this->actingAs($buyer)->patch(route('orders.cancel', $order))->assertRedirect();

    expect($order->fresh()->status)->toBe('shipped');
});

it('does not let a customer cancel someone else\'s order', function () {
    $buyer = User::factory()->create();
    $stranger = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $stranger->id, 'status' => 'pending']);

    $this->actingAs($buyer)->patch(route('orders.cancel', $order))->assertForbidden();
});

it('lets a customer repeat a delivered order by adding its items back to the cart', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'status' => 'active']);
    $order = Order::factory()->create(['user_id' => $buyer->id, 'status' => 'delivered']);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'status' => 'delivered']);

    $this->actingAs($buyer)->post(route('orders.repeat', $order))->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', ['user_id' => $buyer->id, 'product_id' => $product->id, 'quantity' => 2]);
});
