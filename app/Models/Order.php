<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'pending' => 'Ожидает обработки',
        'confirmed' => 'Подтверждён',
        'processing' => 'В обработке',
        'shipped' => 'В пути',
        'delivered' => 'Доставлен',
        'cancelled' => 'Отменён',
    ];

    protected $fillable = [
        'number', 'user_id', 'name', 'idempotency_key', 'access_token', 'promo_code_id', 'promo_code', 'status', 'subtotal', 'discount', 'total',
        'city', 'address', 'phone', 'delivery_method', 'payment_method', 'payment_status', 'tracking_number', 'admin_note', 'cancelled_at', 'stock_restored_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->access_token ??= Str::random(40);
        });
    }

    /** Name of the customer who placed this order, whether or not they have an account. */
    public function customerName(): string
    {
        return $this->user->name ?? $this->name ?? 'Гость';
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

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
