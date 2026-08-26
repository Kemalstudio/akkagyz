<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Console\Command;

/**
 * Orders that never leave "pending" mean nobody at the shop acknowledged
 * them — the customer's stock reservation should not sit there forever.
 */
class CancelStaleOrders extends Command
{
    protected $signature = 'orders:cancel-stale {--hours=48 : Cancel pending orders older than this many hours}';

    protected $description = 'Cancel orders stuck in "pending" for too long and restore their stock';

    public function handle(OrderStatusService $orderStatus): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $stale = Order::where('status', 'pending')->where('created_at', '<', now()->subHours($hours))->get();

        foreach ($stale as $order) {
            $orderStatus->cancel($order, null, "Автоматически отменён: заказ не обработан в течение {$hours} ч.");
            $this->info("Отменён заказ {$order->number}");
        }

        $this->info($stale->count().' заказ(ов) отменено.');

        return self::SUCCESS;
    }
}
