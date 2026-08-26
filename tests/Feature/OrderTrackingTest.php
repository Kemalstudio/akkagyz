<?php

use App\Models\Order;
use App\Models\User;

it('shows the public order tracking page', function () {
    $this->get(route('orders.track'))
        ->assertOk()
        ->assertSee('Где мой заказ?');
});

it('tracks an order using its number and recipient phone', function () {
    $user = User::factory()->create();
    $order = Order::create([
        'number' => 'AK-TRACK-1001',
        'user_id' => $user->id,
        'status' => 'shipped',
        'subtotal' => 145,
        'discount' => 0,
        'total' => 145,
        'city' => 'Ашхабад',
        'address' => 'ул. Атамурата, 10',
        'phone' => '+993 64 00 53 74',
        'delivery_method' => 'courier',
        'payment_method' => 'cash',
    ]);

    $this->post(route('orders.lookup'), [
        'number' => strtolower($order->number),
        'phone' => '+993 (64) 00-53-74',
    ])->assertOk()
        ->assertSee($order->number)
        ->assertSee('Передан в доставку')
        ->assertSee('145 TMT');
});

it('does not expose an order when the phone is incorrect', function () {
    $user = User::factory()->create();
    Order::create([
        'number' => 'AK-PRIVATE-1002',
        'user_id' => $user->id,
        'status' => 'processing',
        'subtotal' => 90,
        'discount' => 0,
        'total' => 90,
        'phone' => '+99364005374',
    ]);

    $this->post(route('orders.lookup'), [
        'number' => 'AK-PRIVATE-1002',
        'phone' => '+99361111111',
    ])->assertRedirect()
        ->assertSessionHasErrors('number');
});
