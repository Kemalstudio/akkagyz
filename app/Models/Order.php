<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'user_id', 'idempotency_key', 'promo_code_id', 'promo_code', 'status', 'subtotal', 'discount', 'total',
        'city', 'address', 'phone', 'delivery_method', 'payment_method', 'payment_status', 'tracking_number', 'admin_note', 'cancelled_at', 'stock_restored_at',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function statusHistories(): HasMany { return $this->hasMany(OrderStatusHistory::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    protected function casts(): array { return ['cancelled_at'=>'datetime','stock_restored_at'=>'datetime']; }
}
