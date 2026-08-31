<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderCancelledForSeller;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Single place that knows how an order is allowed to move between statuses.
 *
 * Admin, seller, mobile and web all funnel through transition()/cancel()/
 * updateItemStatus() so stock restoration, finance postings, status history
 * and customer/seller notifications never happen in only one of those paths.
 */
class OrderStatusService
{
    private const ORDER_STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    private const ITEM_STATUS_FOR_ORDER_STATUS = [
        'pending' => 'pending',
        'confirmed' => 'pending',
        'processing' => 'pending',
        'shipped' => 'shipped',
        'delivered' => 'delivered',
    ];

    public function __construct(private MarketplaceFinanceService $finance) {}

    /**
     * @param  array{actor?: ?User, comment?: ?string, ip_address?: ?string, payment_status?: ?string, tracking_number?: ?string, admin_note?: ?string, sync_items?: bool}  $options
     */
    public function transition(Order $order, string $status, array $options = []): Order
    {
        if (! in_array($status, self::ORDER_STATUSES, true)) {
            throw ValidationException::withMessages(['status' => 'Недопустимый статус заказа.']);
        }

        $syncItems = $options['sync_items'] ?? true;

        return DB::transaction(function () use ($order, $status, $options, $syncItems) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $from = $locked->status;

            if ($from === 'cancelled' && $status !== 'cancelled') {
                throw ValidationException::withMessages(['status' => 'Отменённый заказ нельзя вернуть в работу — оформите его заново.']);
            }

            if ($status === 'cancelled' && ! $locked->stock_restored_at) {
                foreach ($locked->items()->lockForUpdate()->get() as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                        Product::whereKey($item->product_id)->decrement('sales_count', $item->quantity);
                    }
                    $item->update(['status' => 'cancelled']);
                }
                $locked->stock_restored_at = now();
                $locked->cancelled_at = now();
                $this->finance->reverse($locked);
            } elseif ($status !== 'cancelled' && $syncItems) {
                $itemStatus = self::ITEM_STATUS_FOR_ORDER_STATUS[$status] ?? 'pending';
                $locked->items()->where('status', '!=', 'cancelled')->update(['status' => $itemStatus]);
            }

            $locked->status = $status;
            if (! empty($options['tracking_number'])) {
                $locked->tracking_number = $options['tracking_number'];
            }
            if (! empty($options['admin_note'])) {
                $locked->admin_note = $options['admin_note'];
            }
            if (! empty($options['payment_status'])) {
                $locked->payment_status = $options['payment_status'];
            }
            $locked->save();

            if ($locked->status === 'delivered') {
                $this->finance->accrue($locked);
            }

            if ($from !== $locked->status || ! empty($options['comment'])) {
                $locked->statusHistories()->create([
                    'user_id' => ($options['actor'] ?? null)?->id,
                    'from_status' => $from,
                    'to_status' => $locked->status,
                    'comment' => $options['comment'] ?? null,
                    'ip_address' => $options['ip_address'] ?? null,
                ]);
            }

            if (! empty($options['payment_status']) && $options['payment_status'] === 'paid' && ! $locked->payments()->where('status', 'succeeded')->exists()) {
                $locked->payments()->create([
                    'type' => 'payment',
                    'status' => 'succeeded',
                    'amount' => $locked->total,
                    'currency' => BusinessSetting::current()->currency ?: 'TMT',
                    'note' => 'Подтверждено администратором',
                    'processed_at' => now(),
                ]);
            }

            if ($from !== $locked->status) {
                $this->notifyStatusChange($locked->fresh());
            }

            return $locked->fresh();
        });
    }

    /**
     * Customer/system self-service cancellation — only while the order hasn't
     * left "pending"/"processing" yet.
     */
    public function cancel(Order $order, ?User $actor, ?string $reason = null): Order
    {
        if (! in_array($order->status, ['pending', 'confirmed', 'processing'], true)) {
            throw ValidationException::withMessages(['status' => 'Этот заказ уже нельзя отменить.']);
        }

        return $this->transition($order, 'cancelled', [
            'actor' => $actor,
            'comment' => $reason ?: ($actor ? 'Отменён покупателем.' : 'Отменён автоматически.'),
        ]);
    }

    /**
     * A seller updates the status of their own line item. The parent order's
     * aggregate status is then rolled forward to match, through the same
     * transition() so history/finance/notifications stay consistent.
     */
    public function updateItemStatus(OrderItem $item, string $status, User $seller): OrderItem
    {
        abort_unless($item->seller_id === $seller->id, 403);

        if (! in_array($status, ['pending', 'shipped', 'delivered'], true)) {
            throw ValidationException::withMessages(['status' => 'Недопустимый статус.']);
        }

        return DB::transaction(function () use ($item, $status) {
            $locked = OrderItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'cancelled') {
                throw ValidationException::withMessages(['status' => 'Эта позиция заказа отменена.']);
            }
            $locked->update(['status' => $status]);
            $this->recomputeOrderStatus($locked->order_id);

            return $locked->fresh();
        });
    }

    private function recomputeOrderStatus(int $orderId): void
    {
        $order = Order::with('items')->find($orderId);
        if (! $order || $order->status === 'cancelled') {
            return;
        }

        $active = $order->items->where('status', '!=', 'cancelled');
        if ($active->isEmpty()) {
            return;
        }

        $next = match (true) {
            $active->every(fn ($item) => $item->status === 'delivered') => 'delivered',
            $active->every(fn ($item) => in_array($item->status, ['shipped', 'delivered'], true)) => 'shipped',
            $active->contains(fn ($item) => in_array($item->status, ['shipped', 'delivered'], true)) => 'processing',
            default => null,
        };

        $rank = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
        if ($next !== null && ($rank[$next] ?? -1) > ($rank[$order->status] ?? -1)) {
            $this->transition($order, $next, [
                'sync_items' => false,
                'comment' => 'Статус обновлён автоматически по статусам товаров продавцов.',
            ]);
        }
    }

    private function notifyStatusChange(Order $order): void
    {
        $settings = BusinessSetting::current();

        if ($settings->order_notifications && $order->user) {
            $order->user->notify(new OrderStatusChanged($order));
        }

        if ($order->status === 'cancelled' && $settings->seller_notifications) {
            $order->loadMissing('items.seller');
            $order->items->pluck('seller')->filter()->unique('id')
                ->each(fn (User $seller) => $seller->notify(new OrderCancelledForSeller($order)));
        }
    }
}
