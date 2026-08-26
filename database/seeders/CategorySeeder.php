<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            ['name' => 'Канцелярия', 'slug' => 'kancelyariya', 'icon' => 'pen', 'children' => [
                ['Ручки и карандаши', 'pencil'], ['Тетради и блокноты', 'notebook'], ['Маркеры и фломастеры', 'marker'],
                ['Ластики и точилки', 'eraser'], ['Клей и скотч', 'glue'], ['Линейки и чертёжные принадлежности', 'ruler'],
                ['Бумага для печати', 'printer'],
            ]],
            ['name' => 'Офис и бизнес', 'slug' => 'ofis-i-biznes', 'icon' => 'briefcase', 'children' => [
                ['Папки и архивация', 'folder'], ['Органайзеры для стола', 'tray'], ['Степлеры и дыроколы', 'stapler'],
                ['Калькуляторы', 'calculator'], ['Визитницы', 'id-card'], ['Печати и штампы', 'stamp'],
            ]],
            ['name' => 'Творчество и хобби', 'slug' => 'tvorchestvo', 'icon' => 'palette', 'children' => [
                ['Краски и кисти', 'brush'], ['Скетчбуки и альбомы', 'sketchbook'], ['Материалы для лепки', 'clay'],
                ['Наборы для рукоделия', 'scissors'], ['Оригами и аппликация', 'origami'], ['Скрапбукинг', 'scrapbook'],
            ]],
            ['name' => 'Упаковка', 'slug' => 'upakovka', 'icon' => 'box', 'children' => [
                ['Крафт-пакеты', 'bag'], ['Коробки для подарков', 'gift'], ['Упаковочная бумага', 'paper-roll'],
                ['Ленты и банты', 'ribbon'], ['Скотч и стрейч-плёнка', 'tape'], ['Бирки и наклейки', 'tag'],
            ]],
            ['name' => 'Книги и печать', 'slug' => 'knigi-i-pechat', 'icon' => 'book', 'children' => [
                ['Ежедневники', 'planner'], ['Записные книжки', 'notepad'], ['Обложки для книг', 'book-cover'],
                ['Печатная продукция', 'printer'], ['Календари', 'calendar'], ['Постеры и репродукции', 'poster'],
            ]],
            ['name' => 'Электроника', 'slug' => 'elektronika', 'icon' => 'cpu', 'children' => [
                ['Калькуляторы', 'calculator'], ['Флешки и накопители', 'usb'], ['Кабели и зарядки', 'cable'],
                ['Наушники', 'headphones'], ['Компьютерные аксессуары', 'mouse'], ['Батарейки', 'battery'],
            ]],
        ];

        foreach ($tree as $i => $node) {
            $parent = Category::updateOrCreate(
                ['slug' => $node['slug']],
                ['name' => $node['name'], 'icon' => $node['icon'], 'sort_order' => $i, 'parent_id' => null]
            );

            foreach ($node['children'] as $j => $child) {
                [$childName, $childIcon] = $child;
                Category::updateOrCreate(
                    ['slug' => $node['slug'].'-'.\Illuminate\Support\Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'sort_order' => $j, 'icon' => $childIcon]
                );
            }
        }
    }
}
