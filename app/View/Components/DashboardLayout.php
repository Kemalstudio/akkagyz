<?php

namespace App\View\Components;

use App\Models\BusinessSetting;
use Illuminate\View\Component;
use Illuminate\View\View;

class DashboardLayout extends Component
{
    public function __construct(
        public string $title = '',
        public string $active = '',
    ) {}

    public function render(): View
    {
        $user = auth()->user();

        return view('components.dashboard-layout', [
            'businessSettings' => BusinessSetting::current(),
            'notifications' => $user->notifications()->latest()->limit(8)->get(),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
