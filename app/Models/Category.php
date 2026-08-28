<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'icon', 'sort_order'];

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** @return HasOne<Product, $this> */
    public function previewProduct(): HasOne
    {
        return $this->hasOne(Product::class)
            ->customerVisible()
            ->oldestOfMany();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, $this> */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** @return HasMany<Category, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /** @return HasMany<ProductAttribute, $this> */
    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('sort_order');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * This category's id plus every descendant id, at any depth (children,
     * grandchildren, ...). Queried directly against the database so it stays
     * correct regardless of which relations are already eager-loaded.
     *
     * @return array<int, int>
     */
    public function idsWithChildren(): array
    {
        $ids = [$this->id];
        $frontier = [$this->id];

        while ($frontier !== []) {
            $childIds = static::query()->whereIn('parent_id', $frontier)->pluck('id')->all();
            $childIds = array_values(array_diff($childIds, $ids));
            if ($childIds === []) {
                break;
            }
            $ids = [...$ids, ...$childIds];
            $frontier = $childIds;
        }

        return $ids;
    }

    /** Every descendant id (children, grandchildren, ...), excluding this category itself. */
    public function descendantIds(): array
    {
        return array_values(array_diff($this->idsWithChildren(), [$this->id]));
    }

    /** The top-level ancestor of this category (itself, if it has no parent). */
    public function root(): self
    {
        $node = $this;
        while ($node->parent) {
            $node = $node->parent;
        }

        return $node;
    }
}
