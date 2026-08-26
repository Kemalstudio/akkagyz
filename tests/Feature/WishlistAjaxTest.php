<?php

use App\Models\Product;
use App\Models\User;

it('toggles a wishlist item with a json response without page reload', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Тестовый товар',
        'slug' => 'wishlist-ajax-product',
        'price' => 25,
        'stock' => 5,
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->postJson(route('wishlist.toggle', $product))
        ->assertOk()
        ->assertJson(['active' => true, 'count' => 1]);

    $this->assertDatabaseHas('wishlist_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    $this->actingAs($user)
        ->postJson(route('wishlist.toggle', $product))
        ->assertOk()
        ->assertJson(['active' => false, 'count' => 0]);

    $this->assertDatabaseMissing('wishlist_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});
