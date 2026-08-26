<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'status', 'is_verified_purchase', 'moderation_reason', 'moderated_by', 'moderated_at'];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function replies(): HasMany { return $this->hasMany(ReviewReply::class)->oldest(); }
    public function reports(): HasMany { return $this->hasMany(ReviewReport::class); }
    public function scopePublished($query) { return $query->where('status','published'); }
    protected function casts():array{return ['is_verified_purchase'=>'boolean','moderated_at'=>'datetime'];}
}
