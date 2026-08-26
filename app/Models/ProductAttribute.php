<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    protected $fillable = ['category_id', 'name', 'sort_order'];

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductAttributeValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /** Icon for this attribute's filter group, guessed from its name so every filter reads distinctly at a glance. */
    public function getFilterIconAttribute(): string
    {
        $name = mb_strtolower($this->name);

        return match (true) {
            str_contains($name, 'количество'), str_contains($name, 'элемент'), str_contains($name, 'листов'), str_contains($name, 'страниц') => 'grid',
            str_contains($name, 'цвет') => 'palette',
            str_contains($name, 'памят') => 'usb',
            str_contains($name, 'питани'), str_contains($name, 'батаре'), str_contains($name, 'аккумулятор') => 'battery',
            str_contains($name, 'печати') => 'printer',
            str_contains($name, 'бумаг') => 'paper-roll',
            str_contains($name, 'диагональ'), str_contains($name, 'формат'), str_contains($name, 'размер'), str_contains($name, 'длина'), str_contains($name, 'толщин'), str_contains($name, 'ширина'), str_contains($name, 'высота') => 'ruler',
            str_contains($name, 'плотност'), str_contains($name, 'вес'), str_contains($name, 'материал') => 'box',
            str_contains($name, 'тип') => 'tag',
            default => 'list',
        };
    }
}
