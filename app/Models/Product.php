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
        'price', 'compare_price', 'stock', 'status', 'is_vip',
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
}
