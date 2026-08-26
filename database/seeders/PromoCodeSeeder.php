<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        PromoCode::updateOrCreate(
            ['code' => 'AK10'],
            [
                'name' => 'Скидка 10% на заказ',
                'type' => 'percent',
                'value' => 10,
                'minimum_amount' => 0,
                'maximum_discount' => null,
                'usage_limit' => null,
                'per_user_limit' => 1,
                'is_active' => true,
                'starts_at' => null,
                'expires_at' => null,
            ]
        );
    }
}
