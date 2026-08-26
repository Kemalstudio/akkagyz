<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');
        $sellers = User::where('role', 'seller')->pluck('id', 'store_name');

        $products = [
            ['name' => 'Набор гелевых ручек Premium, 12 цветов', 'cat' => 'kancelyariya', 'seller' => 'PaperCo Store', 'brand' => 'PaperCo', 'price' => 4990, 'compare' => 6200, 'stock' => 124, 'sales' => 86, 'rating' => 4.8, 'ratingCount' => 128],
            ['name' => 'Механический карандаш 0.5мм, металл', 'cat' => 'kancelyariya', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoWrite', 'price' => 2100, 'compare' => null, 'stock' => 60, 'sales' => 22, 'rating' => 4.6, 'ratingCount' => 87],
            ['name' => 'Ручка шариковая синяя, упаковка 20 шт', 'cat' => 'kancelyariya', 'seller' => 'PaperCo Store', 'brand' => 'PaperCo', 'price' => 3190, 'compare' => 3750, 'stock' => 210, 'sales' => 340, 'rating' => 4.5, 'ratingCount' => 340],
            ['name' => 'Капиллярные ручки-лайнеры, набор 6 шт', 'cat' => 'kancelyariya', 'seller' => 'ArtLine Store', 'brand' => 'ArtLine', 'price' => 5600, 'compare' => null, 'stock' => 40, 'sales' => 19, 'rating' => 4.9, 'ratingCount' => 19],
            ['name' => 'Перьевая ручка, подарочный набор', 'cat' => 'kancelyariya', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoWrite', 'price' => 14900, 'compare' => null, 'stock' => 15, 'sales' => 56, 'rating' => 4.9, 'ratingCount' => 56],
            ['name' => 'Набор маркеров для скетчинга, 24 шт', 'cat' => 'kancelyariya', 'seller' => 'ArtLine Store', 'brand' => 'OfficePro', 'price' => 9800, 'compare' => null, 'stock' => 0, 'sales' => 12, 'rating' => 0, 'ratingCount' => 0],

            ['name' => 'Органайзер настольный, дерево/металл', 'cat' => 'ofis-i-biznes', 'seller' => 'PaperCo Store', 'brand' => 'PaperCo', 'price' => 8300, 'compare' => null, 'stock' => 32, 'sales' => 31, 'rating' => 4.7, 'ratingCount' => 212],
            ['name' => 'Ежедневник недатированный А5, экокожа', 'cat' => 'ofis-i-biznes', 'seller' => 'PaperCo Store', 'brand' => 'PaperCo', 'price' => 6500, 'compare' => 7800, 'stock' => 48, 'sales' => 65, 'rating' => 4.6, 'ratingCount' => 44],
            ['name' => 'Степлер офисный + скобы, набор', 'cat' => 'ofis-i-biznes', 'seller' => 'PaperCo Store', 'brand' => 'OfficePro', 'price' => 3800, 'compare' => null, 'stock' => 70, 'sales' => 18, 'rating' => 4.3, 'ratingCount' => 21],
            ['name' => 'Папка-регистратор А4, 75мм', 'cat' => 'ofis-i-biznes', 'seller' => 'PaperCo Store', 'brand' => 'OfficePro', 'price' => 1900, 'compare' => null, 'stock' => 150, 'sales' => 90, 'rating' => 4.4, 'ratingCount' => 33],

            ['name' => 'Скетчбук А4 на пружине, 120 л.', 'cat' => 'tvorchestvo', 'seller' => 'ArtLine Store', 'brand' => 'ArtLine', 'price' => 3400, 'compare' => 4000, 'stock' => 8, 'sales' => 54, 'rating' => 4.5, 'ratingCount' => 41],
            ['name' => 'Набор акварельных красок, 24 цвета', 'cat' => 'tvorchestvo', 'seller' => 'ArtLine Store', 'brand' => 'ArtLine', 'price' => 7200, 'compare' => null, 'stock' => 27, 'sales' => 40, 'rating' => 4.8, 'ratingCount' => 62],
            ['name' => 'Пенал на молнии, эко-кожа', 'cat' => 'tvorchestvo', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoWrite', 'price' => 5200, 'compare' => null, 'stock' => 35, 'sales' => 28, 'rating' => 4.6, 'ratingCount' => 18],
            ['name' => 'Стикеры-закладки, набор 5 шт', 'cat' => 'tvorchestvo', 'seller' => 'NoteCraft KZ', 'brand' => 'NoteCraft', 'price' => 1100, 'compare' => null, 'stock' => 200, 'sales' => 76, 'rating' => 4.2, 'ratingCount' => 14],

            ['name' => 'Крафт-пакеты с логотипом, 250 шт', 'cat' => 'upakovka', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoCraft', 'price' => 12500, 'compare' => null, 'stock' => 340, 'sales' => 19, 'rating' => 0, 'ratingCount' => 0],
            ['name' => 'Коробки для подарков, набор 3 размера', 'cat' => 'upakovka', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoCraft', 'price' => 6900, 'compare' => 8200, 'stock' => 54, 'sales' => 24, 'rating' => 4.7, 'ratingCount' => 9],
            ['name' => 'Бумага упаковочная крафт, рулон 10м', 'cat' => 'upakovka', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoCraft', 'price' => 2400, 'compare' => null, 'stock' => 120, 'sales' => 33, 'rating' => 4.4, 'ratingCount' => 11],

            ['name' => 'Блокнот А5 в точку, крафт', 'cat' => 'knigi-i-pechat', 'seller' => 'NoteCraft KZ', 'brand' => 'NoteCraft', 'price' => 2490, 'compare' => null, 'stock' => 90, 'sales' => 41, 'rating' => 4.5, 'ratingCount' => 27],
            ['name' => 'Тетрадь на пружине А5, 96 л.', 'cat' => 'knigi-i-pechat', 'seller' => 'NoteCraft KZ', 'brand' => 'NoteCraft', 'price' => 1800, 'compare' => 2200, 'stock' => 130, 'sales' => 58, 'rating' => 4.3, 'ratingCount' => 16],
            ['name' => 'Обложка для книги, экокожа', 'cat' => 'knigi-i-pechat', 'seller' => 'EcoCraft Мастерская', 'brand' => 'EcoCraft', 'price' => 3200, 'compare' => null, 'stock' => 40, 'sales' => 12, 'rating' => 4.6, 'ratingCount' => 7],

            ['name' => 'Калькулятор инженерный', 'cat' => 'elektronika', 'seller' => 'PaperCo Store', 'brand' => 'OfficePro', 'price' => 9200, 'compare' => null, 'stock' => 22, 'sales' => 15, 'rating' => 4.4, 'ratingCount' => 19],
            ['name' => 'Электронная книга-читалка 6"', 'cat' => 'elektronika', 'seller' => 'PaperCo Store', 'brand' => 'OfficePro', 'price' => 42000, 'compare' => 48000, 'stock' => 6, 'sales' => 9, 'rating' => 4.8, 'ratingCount' => 5],
        ];

        foreach ($products as $index => $p) {
            Product::updateOrCreate(
                ['slug' => Str::slug($p['name']).'-'.($index + 1)],
                [
                    'seller_id' => $sellers[$p['seller']] ?? null,
                    'category_id' => $categories[$p['cat']] ?? null,
                    'name' => $p['name'],
                    'brand' => $p['brand'],
                    'description' => "Качественный товар «{$p['name']}» от продавца {$p['seller']}. Быстрая доставка по Туркменистану, гарантия возврата 14 дней.",
                    'price' => $p['price'],
                    'compare_price' => $p['compare'],
                    'stock' => $p['stock'],
                    'status' => 'active',
                    'rating_avg' => $p['rating'],
                    'rating_count' => $p['ratingCount'],
                    'sales_count' => $p['sales'],
                ]
            );
        }

        // A couple of products still pending admin moderation, for the admin demo.
        Product::updateOrCreate(['slug' => 'termostakan-s-logotipom-pending'], [
            'seller_id' => $sellers['EcoCraft Мастерская'] ?? null,
            'category_id' => $categories['upakovka'] ?? null,
            'name' => 'Термостакан с логотипом',
            'brand' => 'EcoCraft',
            'description' => 'Термостакан на заказ с нанесением логотипа.',
            'price' => 5400,
            'stock' => 50,
            'status' => 'pending',
        ]);

        Product::updateOrCreate(['slug' => 'tetrad-na-pruzhine-a5-pending'], [
            'seller_id' => $sellers['NoteCraft KZ'] ?? null,
            'category_id' => $categories['knigi-i-pechat'] ?? null,
            'name' => 'Тетрадь на пружине А5, крафт-обложка',
            'brand' => 'NoteCraft',
            'description' => 'Новинка от NoteCraft KZ, ожидает проверки.',
            'price' => 2100,
            'stock' => 80,
            'status' => 'pending',
        ]);
    }
}
