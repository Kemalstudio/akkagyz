<?php

use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderForSeller;
use App\Notifications\OrderPlaced;
use Illuminate\Support\Facades\Notification;

it('creates an order from the cart, decrements stock, and notifies buyer and seller', function () {
    Notification::fake();
    $seller = User::factory()->create([
        'role' => 'seller',
        'store_name' => 'AK Store',
        'store_status' => 'approved',
    ]);
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['seller_id' => $seller->id, 'price' => 50, 'stock' => 5]);
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $response = $this->actingAs($buyer)->post(route('checkout.store'), [
        'idempotency_key' => \Illuminate\Support\Str::uuid()->toString(),
        'name' => 'Test Buyer',
        'city' => 'Ашхабад',
        'address' => 'ул. Тестовая 1',
        'phone' => '+99364005374',
        'delivery_method' => 'courier',
        'payment_method' => 'cash',
    ]);

    $order = $buyer->orders()->first();
    $response->assertRedirect(route('orders.show', $order));
    expect($order->total)->toBe(100);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseCount('cart_items', 0);

    Notification::assertSentTo($buyer, OrderPlaced::class);
    Notification::assertSentTo($seller, NewOrderForSeller::class);
});

it('rejects checkout when stock is insufficient', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($buyer)->post(route('checkout.store'), [
        'name' => 'Test Buyer', 'city' => 'Ашхабад', 'address' => 'ул. Тестовая 1', 'phone' => '+99364005374',
        'delivery_method' => 'courier', 'payment_method' => 'cash',
    ])->assertRedirect();

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
});

it('does not create a duplicate order when the same idempotency key is resubmitted', function () {
    $buyer = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);
    $buyer->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);
    $key = \Illuminate\Support\Str::uuid()->toString();

    $payload = [
        'idempotency_key' => $key,
        'name' => 'Test Buyer',
        'city' => 'Ашхабад', 'address' => 'ул. Тестовая 1', 'phone' => '+99364005374',
        'delivery_method' => 'courier', 'payment_method' => 'cash',
    ];

    $this->actingAs($buyer)->post(route('checkout.store'), $payload)->assertRedirect();
    $this->assertDatabaseCount('orders', 1);

    // Resubmitting the exact same form (double-click, retry) must not create a second order.
    $this->actingAs($buyer)->post(route('checkout.store'), $payload)->assertRedirect();
    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
});
