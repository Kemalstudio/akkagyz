<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerTransaction;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Notification;

function makeOrderWithItem(User $buyer, User $seller, int $stock = 4): Order
{
    $product = Product::factory()->create(['seller_id' => $seller->id, 'price' => 100, 'stock' => $stock]);
    $order = Order::factory()->create(['user_id' => $buyer->id, 'subtotal' => 100, 'total' => 100]);
    OrderItem::factory()->create([
        'order_id' => $order->id, 'product_id' => $product->id, 'seller_id' => $seller->id,
        'price' => 100, 'quantity' => 1, 'status' => 'pending',
    ]);

    return $order;
}

it('moves an order through statuses and records history', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $order = makeOrderWithItem($buyer, $seller);

    $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
        'status' => 'processing', 'comment' => 'Взяли в работу',
    ])->assertRedirect();

    $order->refresh();
    expect($order->status)->toBe('processing');
    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id, 'from_status' => 'pending', 'to_status' => 'processing',
    ]);
    Notification::assertSentTo($buyer, OrderStatusChanged::class);
});

it('restores stock and reverses the seller ledger when an order is cancelled', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $order = makeOrderWithItem($buyer, $seller, stock: 4);
    $item = $order->items()->first();

    $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'delivered'])->assertRedirect();
    $this->assertDatabaseHas('seller_transactions', ['order_item_id' => $item->id, 'type' => 'sale']);

    $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'cancelled'])->assertRedirect();

    $this->assertDatabaseHas('products', ['id' => $item->product_id, 'stock' => 5]);
    $this->assertDatabaseHas('seller_transactions', ['order_item_id' => $item->id, 'type' => 'refund']);
    $order->refresh();
    expect($order->status)->toBe('cancelled')->and($order->stock_restored_at)->not->toBeNull();
});

it('accrues seller commission once an order is marked delivered', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $order = makeOrderWithItem($buyer, $seller);
    $item = $order->items()->first();

    $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'delivered'])->assertRedirect();

    $this->assertDatabaseHas('seller_transactions', ['order_item_id' => $item->id, 'type' => 'sale', 'status' => 'available']);
});

it('does not allow reviving a cancelled order', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller']);
    $order = makeOrderWithItem($buyer, $seller);

    $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'cancelled'])->assertRedirect();
    $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'processing'])->assertSessionHasErrors('status');

    expect($order->fresh()->status)->toBe('cancelled');
});

it('renders the admin order workspace with status menus and operational filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buyer = User::factory()->create(['name' => 'Мердан Аманов']);
    Order::factory()->for($buyer)->create([
        'number' => 'AK-WORKSPACE',
        'status' => 'processing',
        'payment_status' => 'paid',
        'total' => 245,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders'))
        ->assertOk()
        ->assertSee('Центр обработки заказов')
        ->assertSee('Расширенный фильтр')
        ->assertSee('AK-WORKSPACE')
        ->assertSee('data-status-picker', false)
        ->assertSee('name="payment_status"', false)
        ->assertSee('name="delivery_method"', false);
});

it('filters admin orders by customer, status, payment and delivery method', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $matchingBuyer = User::factory()->create(['name' => 'Мердан Аманов']);
    $otherBuyer = User::factory()->create(['name' => 'Другой покупатель']);

    Order::factory()->for($matchingBuyer)->create([
        'number' => 'AK-MATCHING',
        'status' => 'processing',
        'payment_status' => 'paid',
        'delivery_method' => 'courier',
    ]);
    Order::factory()->for($otherBuyer)->create([
        'number' => 'AK-HIDDEN',
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'delivery_method' => 'pickup',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.orders', [
            'search' => 'Мердан',
            'status' => 'processing',
            'payment_status' => 'paid',
            'delivery_method' => 'courier',
        ]))
        ->assertOk()
        ->assertSee('AK-MATCHING')
        ->assertDontSee('AK-HIDDEN');
});
