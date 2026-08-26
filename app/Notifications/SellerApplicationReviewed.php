<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SellerApplicationReviewed extends Notification
{
    public function __construct(public bool $approved) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'store',
            'title' => $this->approved ? 'Магазин одобрен' : 'Заявка отклонена',
            'message' => $this->approved
                ? 'Поздравляем! Ваш магазин прошёл проверку и опубликован на площадке.'
                : 'Ваша заявка на регистрацию продавца была отклонена администратором.',
            'url' => route('seller.dashboard'),
        ];
    }
}
