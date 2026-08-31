<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeApprovedSellers($query)
    {
        return $query->where('role', 'seller')->where('store_status', 'approved');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'admin_role','admin_permissions',
        'store_name',
        'store_slug',
        'store_status',
        'store_description',
        'store_address',
        'store_approved_at',
        'phone',
        'delivery_address',
        'avatar_path',
        'is_vip',
        'is_blocked',
        'vip_activated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'store_approved_at' => 'datetime',
            'vip_activated_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
            'is_vip' => 'boolean',
            'admin_permissions' => 'array',
        ];
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function hasAdminPermission(string $permission): bool { return $this->isAdmin() && (blank($this->admin_role) || $this->admin_role === 'super_admin' || in_array($permission, $this->admin_permissions ?? [], true)); }

    public function isApprovedSeller(): bool
    {
        return $this->isSeller() && $this->store_status === 'approved';
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /** @return HasMany<CartItem, $this> */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /** @return HasMany<WishlistItem, $this> */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /** @return HasMany<CompareItem, $this> */
    public function compareItems(): HasMany
    {
        return $this->hasMany(CompareItem::class);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasMany<StoreReview, $this> */
    public function storeReviews(): HasMany
    {
        return $this->hasMany(StoreReview::class, 'seller_id');
    }

    /** @return HasMany<StoreReport, $this> */
    public function storeReports(): HasMany
    {
        return $this->hasMany(StoreReport::class, 'seller_id');
    }

    public function getStoreRatingAttribute(): float
    {
        return round($this->storeReviews()->avg('rating') ?? 0, 1);
    }
    public function getAvatarUrlAttribute(): ?string { return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null; }

    public static function uniqueStoreSlug(string $storeName): string
    {
        $base = \Illuminate\Support\Str::slug($storeName) ?: 'store';
        $slug = $base;
        $i = 1;
        while (static::where('store_slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}
