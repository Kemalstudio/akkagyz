<?php

namespace App\Notifications;

use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledForSeller extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return array_filter(['database', BusinessSetting::current()->email_notifications ? 'mail' : null]);
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'x',
            'title' => 'Заказ отменён',
            'message' => "Заказ {$this->order->number} отменён — товар возвращён на склад.",
            'url' => route('seller.orders.index'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Заказ {$this->order->number} отменён")
            ->greeting("Здравствуйте, {$notifiable->store_name}!")
            ->line("Заказ {$this->order->number} с вашим товаром был отменён.")
            ->line('Остаток товара возвращён на склад, начисление по этому заказу отменено.')
            ->action('Мои заказы', route('seller.orders.index'));
    }
}
