<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'number' => 'AK-'.strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'status' => 'pending',
            'subtotal' => 0,
            'discount' => 0,
            'total' => 0,
            'city' => 'Ашхабад',
            'address' => fake()->streetAddress(),
            'phone' => '+99364005374',
            'delivery_method' => 'courier',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
