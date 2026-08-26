<?php

namespace App\View\Components;

use App\Models\PromoCode;
use Illuminate\View\Component;
use Illuminate\View\View;

class PromoBanner extends Component
{
    public function render(): View
    {
        $promo = PromoCode::query()
            ->where('is_active', true)
            ->whereNull('product_id')
            ->whereNull('category_id')
            ->whereNull('seller_id')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->withCount('usages')
            ->get()
            ->filter(fn (PromoCode $code) => ! $code->usage_limit || $code->usages_count < $code->usage_limit)
            ->sortByDesc('value')
            ->first();

        return view('components.promo-banner', ['promo' => $promo]);
    }
}
