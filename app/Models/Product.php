<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'category_id', 'name', 'slug', 'sku', 'barcode', 'description', 'attributes', 'weight', 'length', 'width', 'height', 'low_stock_threshold', 'seo_title', 'seo_description', 'rejection_reason', 'archived_at',
        'price', 'compare_price', 'stock', 'status', 'is_vip', 'condition',
        'rating_avg', 'rating_count', 'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'is_vip' => 'boolean',
            'attributes' => 'array', 'archived_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<User, $this> */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }

    /** @return HasMany<ProductAttributeValue, $this> */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCustomerVisible($query)
    {
        return $query->active()
            ->whereNull('archived_at')
            ->where(function ($sellerQuery) {
                $sellerQuery->whereNull('seller_id')
                    ->orWhereHas('seller', fn ($seller) => $seller
                        ->approvedSellers()
                        ->where('is_blocked', false));
            });
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeOwn($query)
    {
        return $query->whereNull('seller_id');
    }

    public function scopeMarketplace($query)
    {
        return $query->whereNotNull('seller_id');
    }

    /**
     * Matches products against a free-text search term word-by-word (any
     * matching word qualifies) instead of requiring the whole phrase to
     * appear verbatim — a plain "LIKE %term%" fails for real queries like
     * "синяя ручка" because the words never appear in that exact order next
     * to each other in the product name.
     */
    public function scopeSearch($query, string $term)
    {
        $words = self::searchWords($term);
        if ($words->isEmpty()) {
            return $query;
        }

        return $query->where(function ($nested) use ($words) {
            foreach ($words as $word) {
                $like = '%'.$word.'%';
                $nested->orWhere('name', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like))
                    ->orWhereHas('seller', fn ($seller) => $seller->where('store_name', 'like', $like))
                    ->orWhereHas('attributeValues', fn ($attribute) => $attribute->where('value', 'like', $like));
            }
        });
    }

    /** Orders results so items matching more of the search words rank first. */
    public function scopeOrderByRelevance($query, string $term)
    {
        $words = self::searchWords($term);
        if ($words->isEmpty()) {
            return $query;
        }

        $bindings = [];
        $cases = $words->map(function ($word) use (&$bindings) {
            $bindings[] = '%'.$word.'%';

            return 'CASE WHEN name LIKE ? THEN 1 ELSE 0 END';
        })->implode(' + ');

        return $query->orderByRaw("({$cases}) DESC", $bindings);
    }

    private static function searchWords(string $term)
    {
        return collect(preg_split('/\s+/u', trim($term)) ?: [])
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function isPurchasable(): bool
    {
        if (! $this->isCustomerVisible() || $this->stock < 1) {
            return false;
        }

        return true;
    }

    public function isCustomerVisible(): bool
    {
        if ($this->status !== 'active' || $this->archived_at !== null) {
            return false;
        }

        return $this->seller_id === null
            || ($this->seller?->isApprovedSeller() && ! $this->seller->is_blocked);
    }
}
