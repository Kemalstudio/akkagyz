<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

it('cancels pending orders older than the threshold and restores their stock', function () {
    $product = Product::factory()->create(['stock' => 3]);
    $stale = Order::factory()->create(['status' => 'pending', 'created_at' => now()->subHours(72)]);
    OrderItem::factory()->create(['order_id' => $stale->id, 'product_id' => $product->id, 'quantity' => 2, 'status' => 'pending']);

    $recent = Order::factory()->create(['status' => 'pending', 'created_at' => now()->subHours(2)]);

    $this->artisan('orders:cancel-stale', ['--hours' => 48])->assertSuccessful();

    expect($stale->fresh()->status)->toBe('cancelled');
    expect($recent->fresh()->status)->toBe('pending');
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
});
