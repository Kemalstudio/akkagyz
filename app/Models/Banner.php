<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'subtitle', 'button_text', 'image_path', 'link_url', 'sort_order', 'is_active', 'show_overlay', 'placement'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'show_overlay' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMain($query)
    {
        return $query->whereIn('placement', ['main', 'both']);
    }

    public function scopeForMarketplace($query)
    {
        return $query->whereIn('placement', ['marketplace', 'both']);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }

    /** Real width/height ratio of the uploaded image, used to size the mobile hero box so it never letterboxes. Falls back to a typical wide-banner ratio if the file can't be read. */
    public function getImageAspectRatioAttribute(): float
    {
        $path = Storage::disk('public')->path($this->image_path);
        $size = is_file($path) ? @getimagesize($path) : false;

        return $size && $size[1] > 0 ? $size[0] / $size[1] : 2.4;
    }
}
