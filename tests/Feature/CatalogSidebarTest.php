<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;

function catalogSidebarDocument(string $html): array
{
    $document = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML($html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return [$document, new DOMXPath($document)];
}

it('preserves catalog context when filters are reset and renders removable active filters', function () {
    $category = Category::create([
        'name' => 'Блокноты для sidebar',
        'slug' => 'sidebar-notebooks',
        'sort_order' => 0,
    ]);
    $format = ProductAttribute::create([
        'category_id' => $category->id,
        'name' => 'Формат',
        'sort_order' => 0,
    ]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Sidebar Notebook A5',
        'slug' => 'sidebar-notebook-a5',
        'price' => 100,
        'compare_price' => 130,
        'stock' => 8,
        'status' => 'active',
    ]);
    $product->attributeValues()->create([
        'product_attribute_id' => $format->id,
        'value' => 'A5',
    ]);

    $query = [
        'q' => 'Sidebar Notebook',
        'category' => $category->slug,
        'sort' => 'newest',
        'per_page' => 50,
        'min_price' => 50,
        'max_price' => 120,
        'in_stock' => 1,
        'on_sale' => 1,
        'attr' => [$format->id => ['A5']],
    ];
    $response = $this->get(route('catalog', $query))->assertOk();
    [, $xpath] = catalogSidebarDocument($response->getContent());

    $reset = $xpath->query('//*[@data-filter-reset]')->item(0);
    expect($reset)->not->toBeNull();
    parse_str((string) parse_url($reset->getAttribute('href'), PHP_URL_QUERY), $resetQuery);
    expect($resetQuery)->toBe([
        'q' => 'Sidebar Notebook',
        'category' => $category->slug,
        'sort' => 'newest',
        'per_page' => '50',
    ]);

    expect($xpath->query("//input[@name='in_stock' and @checked]")->length)->toBe(1)
        ->and($xpath->query("//input[@name='attr[{$format->id}][]' and @value='A5' and @checked]")->length)->toBe(1)
        ->and($xpath->query('//*[@data-active-filters]//a[contains(concat(" ", normalize-space(@class), " "), " active-filter-chip ")]')->length)->toBe(4);

    $attributeChip = $xpath->query('//*[@data-active-filters]//a[span[contains(normalize-space(.), "Формат: A5")]]')->item(0);
    expect($attributeChip)->not->toBeNull();
    parse_str((string) parse_url($attributeChip->getAttribute('href'), PHP_URL_QUERY), $attributeRemovalQuery);
    expect($attributeRemovalQuery)->not->toHaveKey('attr')
        ->and($attributeRemovalQuery['min_price'])->toBe('50')
        ->and($attributeRemovalQuery['in_stock'])->toBe('1');
});

it('does not show reset actions when only catalog context is present', function () {
    $category = Category::create([
        'name' => 'Контекстная категория',
        'slug' => 'context-only-category',
        'sort_order' => 0,
    ]);
    Product::create([
        'category_id' => $category->id,
        'name' => 'Context Search Product',
        'slug' => 'context-search-product',
        'price' => 25,
        'stock' => 2,
        'status' => 'active',
    ]);

    $response = $this->get(route('catalog', [
        'q' => 'Context Search',
        'category' => $category->slug,
        'sort' => 'popular',
        'per_page' => 30,
    ]))->assertOk();
    [, $xpath] = catalogSidebarDocument($response->getContent());

    expect($xpath->query('//*[@data-filter-reset]')->length)->toBe(0)
        ->and($xpath->query('//*[@data-active-filters]')->length)->toBe(0);
});

it('renders an accessible responsive filter shell', function (string $routeName) {
    $response = $this->get(route($routeName))->assertOk();
    [, $xpath] = catalogSidebarDocument($response->getContent());

    $drawer = $xpath->query('//*[@data-filter-drawer]')->item(0);
    $opener = $xpath->query('//*[@data-filter-open]')->item(0);
    expect($drawer)->not->toBeNull()
        ->and($drawer->getAttribute('id'))->toBe('catalog-filter-drawer')
        ->and($opener)->not->toBeNull()
        ->and($opener->getAttribute('aria-controls'))->toBe('catalog-filter-drawer')
        ->and($opener->getAttribute('aria-expanded'))->toBe('false')
        ->and($xpath->query('//*[@data-filter-close]')->length)->toBe(1)
        ->and($xpath->query("//input[@name='min_price' and @aria-label='Минимальная цена']")->length)->toBe(1)
        ->and($xpath->query("//input[@name='max_price' and @aria-label='Максимальная цена']")->length)->toBe(1);

    $controls = [];
    foreach ($xpath->query('//*[@data-filter-group-trigger]') as $trigger) {
        $controlId = $trigger->getAttribute('aria-controls');
        expect($controlId)->not->toBe('')
            ->and($trigger->getAttribute('aria-expanded'))->toBeIn(['true', 'false']);
        $controls[] = $controlId;
        $panel = $xpath->query("//*[@id='{$controlId}']")->item(0);
        expect($panel)->not->toBeNull()
            ->and($panel->getAttribute('aria-labelledby'))->toBe($trigger->getAttribute('id'));
    }
    expect($controls)->not->toBeEmpty()
        ->and(array_unique($controls))->toHaveCount(count($controls));
})->with(['catalog', 'marketplace.catalog']);
