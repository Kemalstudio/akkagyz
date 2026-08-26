<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;

function createCatalogProduct(Category $category, string $name, int $price = 1000): Product
{
    return Product::create([
        'category_id' => $category->id,
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name).'-'.\Illuminate\Support\Str::random(6),
        'price' => $price,
        'stock' => 5,
        'status' => 'active',
    ]);
}

it('filters the catalog by a category attribute value', function () {
    $category = Category::create(['name' => 'Мониторы', 'slug' => 'monitory-'.\Illuminate\Support\Str::random(6), 'sort_order' => 0]);
    $diagonal = ProductAttribute::create(['category_id' => $category->id, 'name' => 'Диагональ экрана', 'sort_order' => 0]);

    $monitor27 = createCatalogProduct($category, 'Монитор Samsung 27"');
    $monitor27->attributeValues()->create(['product_attribute_id' => $diagonal->id, 'value' => '27 дюймов']);

    $monitor32 = createCatalogProduct($category, 'Монитор LG 32"');
    $monitor32->attributeValues()->create(['product_attribute_id' => $diagonal->id, 'value' => '32 дюйма']);

    $response = $this->get(route('catalog', ['category' => $category->slug]));
    $response->assertOk()->assertSee('Монитор Samsung 27"')->assertSee('Монитор LG 32"');
    $response->assertSee('Диагональ экрана');

    $filtered = $this->get(route('catalog', [
        'category' => $category->slug,
        'attr' => [$diagonal->id => ['27 дюймов']],
    ]));

    $filtered->assertOk()->assertSee('Монитор Samsung 27"')->assertDontSee('Монитор LG 32"');
});

it('shows a product’s characteristics on its detail page', function () {
    $category = Category::create(['name' => 'Ноутбуки', 'slug' => 'noutbuki-'.\Illuminate\Support\Str::random(6), 'sort_order' => 0]);
    $memory = ProductAttribute::create(['category_id' => $category->id, 'name' => 'Объём памяти', 'sort_order' => 0]);

    $laptop = createCatalogProduct($category, 'Ноутбук ProBook');
    $laptop->attributeValues()->create(['product_attribute_id' => $memory->id, 'value' => '16 ГБ']);

    $this->get(route('products.show', $laptop->slug))
        ->assertOk()
        ->assertSee('Характеристики')
        ->assertSee('Объём памяти')
        ->assertSee('16 ГБ');
});
