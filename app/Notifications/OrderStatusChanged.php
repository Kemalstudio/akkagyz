<?php

namespace App\Notifications;

use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
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
            'icon' => $this->order->status === 'cancelled' ? 'x' : 'truck',
            'title' => $this->order->statusLabel(),
            'message' => "Заказ {$this->order->number}: {$this->summary()}.",
            'url' => route('orders.show', $this->order),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order;
        $currency = BusinessSetting::current()->currency ?: 'TMT';

        $mail = (new MailMessage)
            ->subject("Заказ {$order->number}: {$order->statusLabel()}")
            ->greeting("Здравствуйте, {$notifiable->name}!")
            ->line("Статус вашего заказа {$order->number} изменён: {$this->summary()}.");

        if ($order->status === 'shipped' && $order->tracking_number) {
            $mail->line("Трек-номер: {$order->tracking_number}");
        }

        return $mail->line('Сумма заказа: '.number_format($order->total, 0, '', ' ')." {$currency}")
            ->action('Посмотреть заказ', route('orders.show', $order))
            ->line('Спасибо, что выбираете нас!');
    }

    private function summary(): string
    {
        return match ($this->order->status) {
            'processing' => 'принят в обработку',
            'confirmed' => 'подтверждён',
            'shipped' => 'передан в доставку',
            'delivered' => 'доставлен',
            'cancelled' => 'отменён',
            default => $this->order->statusLabel(),
        };
    }
}
