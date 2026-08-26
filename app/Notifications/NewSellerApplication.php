<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewSellerApplication extends Notification
{
    public function __construct(public User $seller) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'store',
            'title' => 'Новая заявка продавца',
            'message' => "«{$this->seller->store_name}» подал заявку на регистрацию магазина.",
            'url' => route('admin.sellers'),
        ];
    }
}
