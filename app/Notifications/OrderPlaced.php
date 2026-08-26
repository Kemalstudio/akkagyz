<?php

namespace App\Notifications;

use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return array_filter(['database', BusinessSetting::current()->email_notifications ? 'mail' : null]);
    }

    public function toArray($notifiable): array
    {
        $currency = BusinessSetting::current()->currency ?: 'TMT';

        return [
            'icon' => 'cart',
            'title' => 'Заказ оформлен',
            'message' => "Заказ {$this->order->number} на сумму ".number_format($this->order->total, 0, '', ' ')." {$currency} принят в обработку.",
            'url' => route('orders.show', $this->order),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order->loadMissing('items');
        $currency = BusinessSetting::current()->currency ?: 'TMT';

        $mail = (new MailMessage)
            ->subject("Заказ {$order->number} оформлен")
            ->greeting("Здравствуйте, {$notifiable->name}!")
            ->line("Спасибо за заказ! Мы приняли {$order->number} на сумму ".number_format($order->total, 0, '', ' ')." {$currency}.");

        foreach ($order->items as $item) {
            $mail->line("— {$item->product_name} × {$item->quantity}");
        }

        return $mail->action('Посмотреть заказ', route('orders.show', $order))
            ->line('Мы сообщим вам, когда заказ будет передан в доставку.');
    }
}
