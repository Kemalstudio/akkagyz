<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MobilePasswordResetCode extends Notification
{
    public function __construct(public string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Восстановление пароля AK KAGYZ')
            ->line("Код для восстановления пароля: {$this->code}")
            ->line('Код действителен 15 минут.')
            ->line('Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.');
    }
}
