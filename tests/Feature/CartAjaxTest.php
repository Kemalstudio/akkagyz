<?php

use App\Models\Product;
use App\Models\User;

it('updates cart quantity and drawer through ajax', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Бумага для AJAX корзины',
        'slug' => 'ajax-cart-paper',
        'price' => 40,
        'stock' => 2,
        'status' => 'active',
    ]);

    $this->actingAs($user)->postJson(route('cart.add', $product), ['action' => 'increment'])
        ->assertOk()->assertJson(['quantity' => 1, 'count' => 1, 'subtotal' => 40])
        ->assertJsonPath('message', 'Товар в корзине');

    $this->actingAs($user)->postJson(route('cart.add', $product), ['action' => 'increment'])
        ->assertOk()->assertJson(['quantity' => 2, 'count' => 2, 'subtotal' => 80]);

    $this->actingAs($user)->postJson(route('cart.add', $product), ['action' => 'increment'])
        ->assertStatus(422);

    $this->actingAs($user)->postJson(route('cart.add', $product), ['action' => 'decrement'])
        ->assertOk()->assertJson(['quantity' => 1, 'count' => 1]);
});
