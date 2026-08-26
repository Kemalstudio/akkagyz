<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\MobilePasswordResetCode;
use Illuminate\Support\Facades\Notification;

it('serves the mobile product catalog', function () {
    Product::create(['name' => 'API Paper', 'slug' => 'api-paper', 'price' => 50, 'stock' => 8, 'status' => 'active']);

    $this->getJson('/api/v1/products')->assertOk()
        ->assertJsonPath('data.0.name', 'API Paper')
        ->assertJsonPath('data.0.price', 50);
});

it('provides categories filters and product details for mobile catalog', function () {
    $category = Category::create(['name' => 'Office', 'slug' => 'office-mobile', 'sort_order' => 1]);
    $product = Product::create(['category_id' => $category->id, 'name' => 'Filter Paper', 'slug' => 'filter-paper', 'description' => 'Premium paper', 'price' => 75, 'stock' => 10, 'status' => 'active']);

    $this->getJson('/api/v1/categories')->assertOk()->assertJsonPath('data.0.name', 'Office');
    $this->getJson('/api/v1/filters?category_id='.$category->id)->assertOk()
        ->assertJsonPath('data.min_price', 75);
    $this->getJson('/api/v1/products/'.$product->id)->assertOk()
        ->assertJsonPath('data.description', 'Premium paper')->assertJsonStructure(['data' => ['images', 'reviews']]);
    $this->getJson('/api/v1/products?min_price=70&max_price=80')->assertOk()->assertJsonCount(1, 'data');
});

it('authenticates a mobile user and protects private resources', function () {
    User::factory()->create(['email' => 'mobile@example.com', 'password' => 'password123']);

    $token = $this->postJson('/api/v1/auth/login', ['email' => 'mobile@example.com', 'password' => 'password123'])
        ->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']])->json('token');

    $this->getJson('/api/v1/cart')->assertUnauthorized();
    $this->withToken($token)->getJson('/api/v1/cart')->assertOk()->assertJson(['count' => 0, 'subtotal' => 0]);
    $this->withToken($token)->patchJson('/api/v1/me', [
        'name' => 'Updated Mobile User',
        'email' => 'mobile@example.com',
        'phone' => '+99364005374',
    ])->assertOk()->assertJsonPath('data.name', 'Updated Mobile User');
});

it('registers a new customer from the mobile application', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Mobile Customer',
        'email' => 'new-mobile@example.com',
        'phone' => '+99364005374',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'phone']])
        ->assertJsonPath('user.role', 'customer');

    $this->assertDatabaseHas('users', ['email' => 'new-mobile@example.com', 'role' => 'customer']);
});

it('updates cart and wishlist through the mobile api', function () {
    $user = User::factory()->create();
    $product = Product::create(['name' => 'Mobile Notebook', 'slug' => 'mobile-notebook', 'price' => 30, 'stock' => 4, 'status' => 'active']);
    $plain = 'test-mobile-token';
    \App\Models\MobileApiToken::create(['user_id' => $user->id, 'token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDay()]);

    $this->withToken($plain)->postJson("/api/v1/cart/{$product->id}", ['action' => 'increment'])
        ->assertOk()->assertJson(['count' => 1, 'subtotal' => 30]);
    $this->withToken($plain)->postJson("/api/v1/wishlist/{$product->id}")
        ->assertOk()->assertJson(['active' => true, 'count' => 1]);
    $order = \App\Models\Order::create(['number'=>'AK-REVIEW','user_id'=>$user->id,'status'=>'delivered','subtotal'=>30,'discount'=>0,'total'=>30,'payment_status'=>'paid']);
    \App\Models\OrderItem::create(['order_id'=>$order->id,'product_id'=>$product->id,'product_name'=>$product->name,'price'=>30,'quantity'=>1,'status'=>'delivered']);
    $this->withToken($plain)->postJson("/api/v1/products/{$product->id}/reviews", ['rating' => 5, 'comment' => 'Отличный товар для офиса.'])
        ->assertCreated()->assertJsonStructure(['review_id']);
    $this->getJson("/api/v1/products/{$product->id}")->assertOk()->assertJsonPath('data.reviews.0.rating', 5);
});

it('creates, tracks, cancels and repeats a mobile order safely', function () {
    $user = User::factory()->create();
    $product = Product::create(['name' => 'Checkout Paper', 'slug' => 'checkout-paper', 'price' => 40, 'stock' => 3, 'status' => 'active']);
    $plain = 'mobile-checkout-token';
    \App\Models\MobileApiToken::create(['user_id' => $user->id, 'token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDay()]);

    $this->withToken($plain)->postJson("/api/v1/cart/{$product->id}", ['action' => 'increment'])->assertOk();
    $order = $this->withToken($plain)->postJson('/api/v1/checkout', [
        'city' => 'Ashgabat',
        'address' => 'Bitarap Turkmenistan 1',
        'phone' => '+993 64 005374',
        'delivery_method' => 'courier',
        'payment_method' => 'cash',
    ])->assertCreated()->assertJsonPath('data.total', 40)->json('data');

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 2]);
    $this->withToken($plain)->getJson('/api/v1/orders/'.$order['id'])->assertOk()->assertJsonPath('data.items.0.name', 'Checkout Paper');
    $this->postJson('/api/v1/track-order', ['number' => strtolower($order['number']), 'phone' => '99364005374'])
        ->assertOk()->assertJsonPath('data.number', $order['number']);
    $this->postJson('/api/v1/track-order', ['number' => $order['number'], 'phone' => '+99360000000'])->assertNotFound();
    $this->withToken($plain)->patchJson('/api/v1/orders/'.$order['id'].'/cancel')->assertOk()->assertJsonPath('data.status', 'cancelled');
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->withToken($plain)->postJson('/api/v1/orders/'.$order['id'].'/repeat')->assertOk()->assertJsonPath('count', 1);
});

it('does not create a mobile order when stock is insufficient', function () {
    $user = User::factory()->create();
    $product = Product::create(['name' => 'Limited Paper', 'slug' => 'limited-paper', 'price' => 25, 'stock' => 1, 'status' => 'active']);
    $plain = 'mobile-stock-token';
    \App\Models\MobileApiToken::create(['user_id' => $user->id, 'token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDay()]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->withToken($plain)->postJson('/api/v1/checkout', [
        'city' => 'Ashgabat', 'address' => 'Test street 1', 'phone' => '+99364005374',
        'delivery_method' => 'courier', 'payment_method' => 'cash',
    ])->assertUnprocessable();

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
});

it('resets a forgotten mobile password with an emailed code', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'forgot@example.com', 'password' => 'oldpassword']);

    $this->postJson('/api/v1/auth/forgot-password', ['email' => 'forgot@example.com'])
        ->assertOk();

    $code = null;
    Notification::assertSentTo($user, MobilePasswordResetCode::class, function ($notification) use (&$code) {
        $code = $notification->code;

        return true;
    });
    expect($code)->not->toBeNull();

    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'forgot@example.com',
        'code' => 'wrong-code',
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertUnprocessable();

    $this->postJson('/api/v1/auth/reset-password', [
        'email' => 'forgot@example.com',
        'code' => $code,
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertOk();

    $this->postJson('/api/v1/auth/login', ['email' => 'forgot@example.com', 'password' => 'newpassword1'])
        ->assertOk();
});

it('does not reveal whether an email is registered when requesting a reset code', function () {
    Notification::fake();

    $this->postJson('/api/v1/auth/forgot-password', ['email' => 'nobody@example.com'])
        ->assertOk();

    Notification::assertNothingSent();
});

it('changes the password for an authenticated mobile user and revokes other sessions', function () {
    $user = User::factory()->create(['password' => 'currentpassword']);
    $plain = 'mobile-password-token';
    \App\Models\MobileApiToken::create(['user_id' => $user->id, 'token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDay()]);
    $otherPlain = 'mobile-other-token';
    \App\Models\MobileApiToken::create(['user_id' => $user->id, 'token_hash' => hash('sha256', $otherPlain), 'expires_at' => now()->addDay()]);

    $this->withToken($plain)->patchJson('/api/v1/me/password', [
        'current_password' => 'wrong-password',
        'password' => 'brandnewpassword',
        'password_confirmation' => 'brandnewpassword',
    ])->assertUnprocessable();

    $this->withToken($plain)->patchJson('/api/v1/me/password', [
        'current_password' => 'currentpassword',
        'password' => 'brandnewpassword',
        'password_confirmation' => 'brandnewpassword',
    ])->assertOk();

    $this->assertDatabaseCount('mobile_api_tokens', 1);
    $this->withToken($otherPlain)->getJson('/api/v1/me')->assertUnauthorized();
    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'brandnewpassword'])->assertOk();
});
