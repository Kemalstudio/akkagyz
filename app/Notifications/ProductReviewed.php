<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Notification;

class ProductReviewed extends Notification
{
    public function __construct(public Product $product, public bool $approved) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'package',
            'title' => $this->approved ? 'Товар опубликован' : 'Товар отклонён',
            'message' => $this->approved
                ? "«{$this->product->name}» прошёл модерацию и опубликован на витрине."
                : "«{$this->product->name}» был отклонён администратором.",
            'url' => route('seller.products.index'),
        ];
    }
}
