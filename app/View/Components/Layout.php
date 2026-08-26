<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\BusinessSetting;
use Illuminate\View\Component;
use Illuminate\View\View;

class Layout extends Component
{
    public function __construct(public ?string $title = null) {}

    public function render(): View
    {
        $user = auth()->user();

        return view('components.layout', [
            'businessSettings' => BusinessSetting::current(),
            'navCategories' => Category::topLevel()->orderBy('sort_order')
                ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
                ->get(),
            'cartCount' => $user ? $user->cartItems()->sum('quantity') : 0,
            'wishlistCount' => $user ? $user->wishlistItems()->count() : 0,
            'compareCount' => $user ? $user->compareItems()->count() : 0,
            'notifications' => $user ? $user->notifications()->latest()->limit(8)->get() : collect(),
            'unreadCount' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }
}
