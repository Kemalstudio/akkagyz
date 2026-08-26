<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Канцтовары и упаковка для вашего бизнеса',
                'subtitle' => 'До −30% на товары для офиса и учёбы. Более 12 000 товаров от проверенных продавцов Туркменистана.',
                'button_text' => 'Смотреть акции',
                'image_path' => 'banners/stationery-desk.jpg',
                'link_url' => '/catalog',
                'sort_order' => 0,
            ],
            [
                'title' => 'Эко-упаковка из переработанной бумаги',
                'subtitle' => 'Новая коллекция крафтовой упаковки от локальных мастерских Туркменистана.',
                'button_text' => 'Смотреть коллекцию',
                'image_path' => 'banners/kraft-packaging.jpg',
                'link_url' => '/catalog?category=upakovka',
                'sort_order' => 1,
            ],
            [
                'title' => 'Всё для творчества и хобби',
                'subtitle' => 'Краски, скетчбуки и материалы для художников — в одном месте.',
                'button_text' => 'В каталог творчества',
                'image_path' => 'banners/watercolor-paints.jpg',
                'link_url' => '/catalog?category=tvorchestvo',
                'sort_order' => 2,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(['image_path' => $banner['image_path']], $banner);
        }
    }
}
