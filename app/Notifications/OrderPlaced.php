<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'cart',
            'title' => 'Заказ оформлен',
            'message' => "Заказ {$this->order->number} на сумму ".number_format($this->order->total, 0, '', ' ').' ₸ принят в обработку.',
            'url' => route('orders.show', $this->order),
        ];
    }
}
