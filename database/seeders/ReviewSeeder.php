<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::active()->inRandomOrder()->limit($customers->count() * 3)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $comments = [
            5 => [
                'Заказываю уже не первый раз — качество стабильно хорошее, а доставка приходит даже быстрее заявленного срока.',
                'Именно то, что искала для офиса. Упаковано аккуратно, товар как на фото.',
                'Отличное соотношение цены и качества. Буду брать ещё.',
                'Продавец на связи, ответил на все вопросы до заказа. Товар полностью соответствует описанию.',
            ],
            4 => [
                'Хорошее качество, но доставка заняла на день дольше обещанного.',
                'В целом довольна покупкой, упаковка могла быть и понадёжнее.',
                'Товар хороший, цена немного выше, чем ожидала, но в целом рекомендую.',
            ],
        ];

        $index = 0;
        foreach ($customers as $customer) {
            for ($i = 0; $i < 3 && $index < $products->count(); $i++, $index++) {
                $rating = array_rand($comments);
                $product = $products[$index];

                Review::updateOrCreate(
                    ['product_id' => $product->id, 'user_id' => $customer->id],
                    [
                        'rating' => $rating,
                        'comment' => $comments[$rating][array_rand($comments[$rating])],
                        'status' => 'published',
                        'is_verified_purchase' => true,
                    ]
                );
            }
        }
    }
}
