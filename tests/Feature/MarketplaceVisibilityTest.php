<?php

use App\Models\Product;
use App\Models\User;

it('renders the professional marketplace hero with the generated background', function () {
    $this->get(route('marketplace.home'))
        ->assertOk()
        ->assertSee('Товары от независимых магазинов Туркменистана')
        ->assertSee('images/marketplace/marketplace-hero.jpg', false)
        ->assertSee('Открыть каталог')
        ->assertSee('Начать продавать');
});

it('shows only moderated products from approved unblocked sellers', function () {
    $approvedSeller = User::factory()->create([
        'role' => 'seller',
        'store_status' => 'approved',
        'store_name' => 'Visible Store',
        'store_slug' => 'visible-store',
        'is_blocked' => false,
    ]);
    $blockedSeller = User::factory()->create([
        'role' => 'seller',
        'store_status' => 'approved',
        'store_name' => 'Blocked Store',
        'store_slug' => 'blocked-store',
        'is_blocked' => true,
    ]);

    $visible = Product::factory()->create([
        'seller_id' => $approvedSeller->id,
        'name' => 'Visible Marketplace Product',
        'slug' => 'visible-marketplace-product',
    ]);
    $pending = Product::factory()->create([
        'seller_id' => $approvedSeller->id,
        'name' => 'Pending Marketplace Product',
        'slug' => 'pending-marketplace-product',
        'status' => 'pending',
    ]);
    $blocked = Product::factory()->create([
        'seller_id' => $blockedSeller->id,
        'name' => 'Blocked Seller Product',
        'slug' => 'blocked-seller-product',
    ]);
    $archived = Product::factory()->create([
        'seller_id' => $approvedSeller->id,
        'name' => 'Archived Marketplace Product',
        'slug' => 'archived-marketplace-product',
        'archived_at' => now(),
    ]);

    $this->get(route('marketplace.catalog'))
        ->assertOk()
        ->assertSee($visible->name)
        ->assertDontSee($pending->name)
        ->assertDontSee($blocked->name)
        ->assertDontSee($archived->name);

    $this->get(route('marketplace.products.show', $visible->slug))->assertOk();
    $this->get(route('marketplace.products.show', $pending->slug))->assertNotFound();
    $this->get(route('marketplace.products.show', $blocked->slug))->assertNotFound();
    $this->get(route('marketplace.products.show', $archived->slug))->assertNotFound();

    $this->get(route('stores.index'))
        ->assertOk()
        ->assertSee($approvedSeller->store_name)
        ->assertDontSee($blockedSeller->store_name);
});
