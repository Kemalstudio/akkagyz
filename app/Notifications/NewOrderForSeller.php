<?php

namespace App\Notifications;

use App\Models\OrderItem;
use Illuminate\Notifications\Notification;

class NewOrderForSeller extends Notification
{
    public function __construct(public OrderItem $orderItem) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'package',
            'title' => 'Новый заказ',
            'message' => "«{$this->orderItem->product_name}» ×{$this->orderItem->quantity} — новый заказ на ".number_format($this->orderItem->price * $this->orderItem->quantity, 0, '', ' ').' ₸.',
            'url' => route('seller.orders.index'),
        ];
    }
}
