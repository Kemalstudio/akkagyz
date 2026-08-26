<?php

namespace App\Notifications;

use App\Models\BusinessSetting;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderForSeller extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public OrderItem $orderItem) {}

    public function via($notifiable): array
    {
        return array_filter(['database', BusinessSetting::current()->email_notifications ? 'mail' : null]);
    }

    public function toArray($notifiable): array
    {
        $currency = BusinessSetting::current()->currency ?: 'TMT';

        return [
            'icon' => 'package',
            'title' => 'Новый заказ',
            'message' => "«{$this->orderItem->product_name}» ×{$this->orderItem->quantity} — новый заказ на ".number_format($this->orderItem->price * $this->orderItem->quantity, 0, '', ' ')." {$currency}.",
            'url' => route('seller.orders.index'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $currency = BusinessSetting::current()->currency ?: 'TMT';
        $total = number_format($this->orderItem->price * $this->orderItem->quantity, 0, '', ' ');

        return (new MailMessage)
            ->subject('Новый заказ на ваш товар')
            ->greeting("Здравствуйте, {$notifiable->store_name}!")
            ->line("У вас новый заказ: «{$this->orderItem->product_name}» × {$this->orderItem->quantity} на {$total} {$currency}.")
            ->action('Мои заказы', route('seller.orders.index'))
            ->line('Пожалуйста, подготовьте товар к отправке.');
    }
}
