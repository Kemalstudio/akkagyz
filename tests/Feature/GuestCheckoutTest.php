<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

it('lets a guest add a product to the cart without an account', function () {
    $product = Product::factory()->create(['stock' => 5]);

    $this->post(route('cart.add', $product))->assertRedirect();

    $item = CartItem::first();
    expect($item->user_id)->toBeNull();
    expect($item->guest_token)->not->toBeNull();
    expect($item->quantity)->toBe(1);
});

it('lets a guest complete checkout and view their order confirmation', function () {
    $product = Product::factory()->create(['stock' => 5, 'price' => 100]);
    $this->post(route('cart.add', $product))->assertRedirect();

    $response = $this->post(route('checkout.store'), [
        'name' => 'Guest Buyer',
        'city' => 'Ashgabat',
        'address' => 'Test street 1',
        'phone' => '+99364005374',
        'delivery_method' => 'courier',
        'payment_method' => 'cash',
    ]);

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect($order->user_id)->toBeNull();
    expect($order->name)->toBe('Guest Buyer');
    expect($order->access_token)->not->toBeNull();
    $this->assertDatabaseCount('cart_items', 0);

    $response->assertRedirect(route('orders.show.guest', ['order' => $order->id, 'token' => $order->access_token]));
    $this->get(route('orders.show.guest', ['order' => $order->id, 'token' => $order->access_token]))->assertOk();
    $this->get(route('orders.show.guest', ['order' => $order->id, 'token' => 'wrong-token']))->assertNotFound();
});

it('blocks a guest from applying a promo code at checkout', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $this->post(route('cart.add', $product))->assertRedirect();

    $this->post(route('checkout.store'), [
        'name' => 'Guest Buyer', 'city' => 'Ashgabat', 'address' => 'Test street 1', 'phone' => '+99364005374',
        'delivery_method' => 'courier', 'payment_method' => 'cash', 'promo_code' => 'SAVE10',
    ])->assertRedirect();

    $this->assertDatabaseCount('orders', 0);
});

it('still requires an account for wishlist and compare', function () {
    $product = Product::factory()->create();

    $this->post(route('wishlist.toggle', $product))->assertRedirect(route('login'));
    $this->post(route('compare.toggle', $product))->assertRedirect(route('login'));
});

it('merges a guest cart into the account on login', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5]);

    $this->post(route('cart.add', $product))->assertRedirect();
    expect(CartItem::whereNull('user_id')->count())->toBe(1);

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect();

    expect(CartItem::whereNull('user_id')->count())->toBe(0);
    expect($user->fresh()->cartItems()->count())->toBe(1);
});
