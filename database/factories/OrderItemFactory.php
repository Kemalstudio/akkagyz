<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => ucfirst(fake()->words(2, true)),
            'price' => fake()->numberBetween(20, 200),
            'quantity' => fake()->numberBetween(1, 3),
            'status' => 'pending',
        ];
    }
}
