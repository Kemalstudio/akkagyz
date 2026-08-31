<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function track()
    {
        return view('storefront.order-tracking');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'string', 'max:30'],
        ], [
            'number.required' => 'Введите номер заказа.',
            'phone.required' => 'Введите телефон, указанный при оформлении.',
        ]);

        $number = strtoupper(trim($data['number']));
        $phone = preg_replace('/\D+/', '', $data['phone']);
        $order = Order::query()
            ->with('items.product')
            ->whereRaw('UPPER(number) = ?', [$number])
            ->first();

        $orderPhone = $order ? preg_replace('/\D+/', '', (string) $order->phone) : '';
        if (! $order || $phone === '' || ! hash_equals($orderPhone, $phone)) {
            return back()
                ->withInput($request->only('number', 'phone'))
                ->withErrors(['number' => 'Заказ не найден. Проверьте номер заказа и телефон.']);
        }

        return view('storefront.order-tracking', compact('order'));
    }

    /** Guest order confirmation, reached via the private link handed back right after checkout. */
    public function showGuest(Order $order, string $token)
    {
        abort_unless($order->access_token && hash_equals($order->access_token, $token), 404);

        $order->load('items.product');

        return view('storefront.order-tracking', ['order' => $order]);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
            'q' => ['nullable', 'string', 'max:40'],
        ]);
        $base = $request->user()->orders();
        $stats = [
            'all' => (clone $base)->count(),
            'active' => (clone $base)->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
        ];
        $orders = $base
            ->with(['items.product.images'])
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($data['q'] ?? null, fn ($query, $search) => $query->where('number', 'like', '%'.trim($search).'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('storefront.orders', compact('orders', 'stats'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items.product.images', 'items.seller', 'statusHistories.user']);

        return view('storefront.order-show', compact('order'));
    }

    public function cancel(Request $request, Order $order, OrderStatusService $orderStatus)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:300']]);

        try {
            $orderStatus->cancel($order, $request->user(), $data['reason'] ?? null);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('status', 'Заказ отменён.');
    }

    public function repeat(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items.product');
        $added = 0;
        foreach ($order->items as $item) {
            $item->product?->loadMissing('seller');
            if ($item->product?->isPurchasable()) {
                $request->user()->cartItems()->updateOrCreate(
                    ['product_id' => $item->product_id],
                    ['quantity' => min($item->quantity, $item->product->stock)]
                );
                $added++;
            }
        }

        return redirect()->route('cart.index')->with(
            $added ? 'status' : 'error',
            $added ? 'Товары добавлены в корзину.' : 'Товары из этого заказа больше недоступны.'
        );
    }
}
