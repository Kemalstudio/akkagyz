<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $this->reassignPaperProducts();
        $this->seedPaper();
        $this->seedElektronika();

        // Values below are parsed straight out of each product's own name
        // (page counts, colors, sizes, capacities already printed there) —
        // not invented — so every filter reflects real catalog data.
        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Тип разлиновки', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'тонкая линия') => 'Тонкая линия',
                str_contains($lower, 'клетка') => 'Клетка',
                str_contains($lower, 'линия') => 'Линия',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Количество листов', function (string $name) {
            if (preg_match('/(\d+)\s*(?:страниц\w*|лист\w*)/ui', $name, $m)) {
                return $m[1].' листов';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Формат', function (string $name) {
            if (preg_match('/\b[AА](3|4|5)\b/u', $name, $m)) {
                return 'A'.$m[1];
            }
            return null;
        }, sortOrder: 3);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Цвет чернил', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'син') => 'Синий',
                str_contains($lower, 'красн') => 'Красный',
                str_contains($lower, 'черн') => 'Чёрный',
                str_contains($lower, 'зелен') => 'Зелёный',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Толщина линии', function (string $name) {
            if (preg_match('/(\d+[.,]\d+)\s*мм/u', $name, $m)) {
                return str_replace(',', '.', $m[1]).' мм';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'механическ') => 'Механический карандаш',
                str_contains($lower, 'карандаш') => 'Карандаш',
                str_contains($lower, 'геловый'), str_contains($lower, 'гелевая') => 'Гелевая ручка',
                str_contains($lower, 'ручка') => 'Шариковая ручка',
                default => null,
            };
        }, sortOrder: 3);

        $colorCount = function (string $name) {
            if (preg_match('/(\d+)\s*цвет/ui', $name, $m)) {
                return $m[1].' цветов';
            }
            return null;
        };
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Количество цветов', $colorCount, sortOrder: 1);
        $this->seedFromName('tvorchestvo-kraski-i-kisti', 'Количество цветов', $colorCount, sortOrder: 1);

        $this->seedFromName('kancelyariya-klei-i-skotc', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'скотч'), str_contains($lower, 'скочь') => 'Скотч',
                str_contains($lower, 'клей') => 'Клей',
                str_contains($lower, 'замаск') => 'Замазка',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Длина', function (string $name) {
            if (preg_match('/(\d+)\s*см/u', $name, $m)) {
                return $m[1].' см';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('elektronika-fleski-i-nakopiteli', 'Объём памяти', function (string $name) {
            if (preg_match('/(\d+)\s*[Gg][Bb]/', $name, $m)) {
                return $m[1].' ГБ';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('tvorchestvo-nabory-dlia-rukodeliia', 'Количество элементов', function (string $name) {
            if (preg_match('/(\d+)\s*элемент/ui', $name, $m)) {
                return $m[1].' элементов';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('tvorchestvo-materialy-dlia-lepki', 'Количество цветов', $colorCount, sortOrder: 1);

        $this->seedFromName('tvorchestvo-sketcbuki-i-albomy', 'Количество страниц', function (string $name) {
            if (preg_match('/(\d+)\s*(?:стр\w*|[Ss]ahypalyk)/u', $name, $m)) {
                return $m[1].' страниц';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('upakovka-korobki-dlia-podarkov', 'Размер', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'маленьк') => 'Маленький',
                str_contains($lower, 'средн') => 'Средний',
                str_contains($lower, 'больш') => 'Большой',
                default => null,
            };
        }, sortOrder: 1);
    }

    /** Create/refresh a category attribute (looked up by category slug) and fill it from a per-product name parser. */
    private function seedFromName(string $categorySlug, string $attributeName, \Closure $extractValue, int $sortOrder): void
    {
        $category = Category::where('slug', $categorySlug)->first();
        if ($category) {
            $this->seedFromCategoryId($category->id, $attributeName, $extractValue, $sortOrder);
        }
    }

    /**
     * The seed catalog originally dumped every printing/photo/note paper
     * product straight into the root "Канцелярия" category alongside school
     * bags, scissors etc. AK KAGYZ ("white paper") is the store's namesake
     * product line, so it gets its own category — matching how the client's
     * real storefront (ynamdar.com) already organizes it under "Çap etmek
     * üçin kagyzlar".
     */
    private function reassignPaperProducts(): void
    {
        $paperCategory = Category::where('name', 'Бумага для печати')->first();
        if (! $paperCategory) {
            return;
        }

        Product::where('category_id', 1)
            ->where('name', 'like', '%бумага%')
            ->update(['category_id' => $paperCategory->id]);
    }

    private function seedPaper(): void
    {
        $category = Category::where('name', 'Бумага для печати')->first();
        if (! $category) {
            return;
        }

        $this->seedFromCategoryId($category->id, 'Формат', function (string $name) {
            if (preg_match('/\b[AА](3|4|5)\b/u', $name, $m)) {
                return 'A'.$m[1];
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromCategoryId($category->id, 'Плотность', function (string $name) {
            if (preg_match('/(\d+)\s*г(?:р)?\b/ui', $name, $m)) {
                return $m[1].' г/м²';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromCategoryId($category->id, 'Количество листов', function (string $name) {
            if (preg_match('/(\d+)\s*(?:шт\w*|лист\w*|стр\w*)/ui', $name, $m)) {
                return $m[1].' листов';
            }
            return null;
        }, sortOrder: 3);

        $this->seedFromCategoryId($category->id, 'Тип печати', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'двухсторон'), str_contains($lower, 'двусторон') => 'Двусторонняя',
                str_contains($lower, 'односторон') => 'Односторонняя',
                default => null,
            };
        }, sortOrder: 4);

        $this->seedFromCategoryId($category->id, 'Тип бумаги', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'фотобумага'), str_contains($lower, 'глянцевая') => 'Фотобумага',
                str_contains($lower, 'визиток') => 'Для визиток',
                str_contains($lower, 'миллиметров') => 'Миллиметровая (чертёжная)',
                str_contains($lower, 'факс') => 'Факс-бумага',
                str_contains($lower, 'сублимацион') => 'Сублимационная',
                str_contains($lower, 'для заметок'), str_contains($lower, 'стикер') => 'Для заметок',
                str_contains($lower, 'цветн') => 'Цветная',
                str_contains($lower, 'картон') => 'Картон',
                default => 'Офисная (для печати)',
            };
        }, sortOrder: 5);
    }

    /** Same as seedFromName(), but for a category already resolved by id (used after reassignment). */
    private function seedFromCategoryId(int $categoryId, string $attributeName, \Closure $extractValue, int $sortOrder): void
    {
        $attribute = ProductAttribute::updateOrCreate(
            ['category_id' => $categoryId, 'name' => $attributeName],
            ['sort_order' => $sortOrder]
        );

        Product::where('category_id', $categoryId)->get(['id', 'name'])->each(function (Product $product) use ($attribute, $extractValue) {
            $value = $extractValue($product->name);
            if ($value === null) {
                return;
            }

            $product->attributeValues()->updateOrCreate(
                ['product_attribute_id' => $attribute->id],
                ['value' => $value]
            );
        });
    }

    private function seedElektronika(): void
    {
        $category = Category::where('slug', 'elektronika')->first();
        if (! $category) {
            return;
        }

        $screenDiagonal = ProductAttribute::updateOrCreate(
            ['category_id' => $category->id, 'name' => 'Диагональ экрана'],
            ['sort_order' => 1]
        );
        $powerSource = ProductAttribute::updateOrCreate(
            ['category_id' => $category->id, 'name' => 'Источник питания'],
            ['sort_order' => 2]
        );

        $reader = Product::where('category_id', $category->id)->where('name', 'like', 'Электронная книга%')->first();
        $reader?->attributeValues()->updateOrCreate(
            ['product_attribute_id' => $screenDiagonal->id],
            ['value' => '6 дюймов']
        );

        $fanPowerSource = [
            'USB', 'USB', 'USB',
            'Батарейки', 'Батарейки', 'Батарейки',
            'Аккумулятор', 'Аккумулятор', 'Аккумулятор', 'Аккумулятор',
        ];

        Product::where('category_id', $category->id)
            ->where('name', 'like', 'Вентилятор%')
            ->orderBy('id')
            ->get()
            ->each(function (Product $product, int $index) use ($powerSource, $fanPowerSource) {
                $value = $fanPowerSource[$index] ?? $fanPowerSource[array_rand($fanPowerSource)];
                $product->attributeValues()->updateOrCreate(
                    ['product_attribute_id' => $powerSource->id],
                    ['value' => $value]
                );
            });
    }
}
