<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('lets a seller update the status of their own order item', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['seller_id' => $seller->id]);
    $order = Order::factory()->create(['user_id' => $buyer->id]);
    $item = OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'seller_id' => $seller->id, 'status' => 'pending']);

    $this->actingAs($seller)->patch(route('seller.orders.update', $item), ['status' => 'shipped'])->assertRedirect();

    expect($item->fresh()->status)->toBe('shipped');
});

it('forbids a seller from updating another seller\'s order item', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $otherSeller = User::factory()->create(['role' => 'seller']);
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['seller_id' => $otherSeller->id]);
    $order = Order::factory()->create(['user_id' => $buyer->id]);
    $item = OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'seller_id' => $otherSeller->id]);

    $this->actingAs($seller)->patch(route('seller.orders.update', $item), ['status' => 'shipped'])->assertForbidden();
});

it('promotes a single-seller order to delivered once its only item is marked delivered', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['seller_id' => $seller->id]);
    $order = Order::factory()->create(['user_id' => $buyer->id, 'status' => 'pending']);
    $item = OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'seller_id' => $seller->id, 'price' => 100, 'quantity' => 1, 'status' => 'pending']);

    $this->actingAs($seller)->patch(route('seller.orders.update', $item), ['status' => 'shipped']);
    expect($order->fresh()->status)->toBe('shipped');

    $this->actingAs($seller)->patch(route('seller.orders.update', $item), ['status' => 'delivered']);
    expect($order->fresh()->status)->toBe('delivered');
    $this->assertDatabaseHas('seller_transactions', ['order_item_id' => $item->id, 'type' => 'sale']);
});
