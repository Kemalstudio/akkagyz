<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $statusLabels = Order::STATUS_LABELS;
        $paymentLabels = [
            'unpaid' => 'Не оплачен',
            'pending' => 'Ожидает оплаты',
            'paid' => 'Оплачен',
            'failed' => 'Ошибка оплаты',
            'refunded' => 'Возвращён',
        ];

        $status = in_array($request->input('status'), [...array_keys($statusLabels), 'active'], true)
            ? $request->input('status')
            : null;
        $paymentStatus = in_array($request->input('payment_status'), array_keys($paymentLabels), true)
            ? $request->input('payment_status')
            : null;
        $deliveryMethod = in_array($request->input('delivery_method'), ['courier', 'pickup'], true)
            ? $request->input('delivery_method')
            : null;
        $sort = in_array($request->input('sort'), ['latest', 'oldest', 'total_desc', 'total_asc'], true)
            ? $request->input('sort')
            : 'latest';
        $perPage = in_array($request->integer('per_page'), [15, 30, 50], true)
            ? $request->integer('per_page')
            : 15;
        $search = mb_substr(trim((string) $request->input('search')), 0, 120);

        $query = Order::with('user')->withCount('items');
        $query->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
            $term = '%'.$search.'%';
            $q->where('number', 'like', $term)
                ->orWhere('name', 'like', $term)
                ->orWhere('phone', 'like', $term)
                ->orWhere('city', 'like', $term)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }));
        $query->when($status === 'active', fn ($q) => $q->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped']));
        $query->when($status && $status !== 'active', fn ($q) => $q->where('status', $status));
        $query->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus));
        $query->when($deliveryMethod, fn ($q) => $q->where('delivery_method', $deliveryMethod));
        $query->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('date_from')));
        $query->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('date_to')));

        match ($sort) {
            'oldest' => $query->oldest(),
            'total_desc' => $query->orderByDesc('total')->orderByDesc('id'),
            'total_asc' => $query->orderBy('total')->orderByDesc('id'),
            default => $query->latest(),
        };

        $orders = $query->paginate($perPage)->withQueryString();
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $overview = [
            'total' => (int) $statusCounts->sum(),
            'today' => Order::whereDate('created_at', today())->count(),
            'active' => (int) collect(['pending', 'confirmed', 'processing', 'shipped'])->sum(fn ($key) => $statusCounts->get($key, 0)),
            'revenue' => (int) Order::where('status', 'delivered')->sum('total'),
        ];

        return view('admin.orders', compact(
            'orders',
            'overview',
            'paymentLabels',
            'statusCounts',
            'statusLabels',
        ));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images', 'items.seller', 'payments', 'statusHistories.user']);

        return view('admin.orders.show', compact('order'));
    }

    public function print(Order $order)
    {
        $order->load(['user', 'items.seller']);
        $businessSettings = BusinessSetting::current();

        return view('admin.orders.print', compact('order', 'businessSettings'));
    }

    public function update(Request $request, Order $order, OrderStatusService $orderStatus)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled'], 'payment_status' => ['nullable', 'in:unpaid,pending,paid,failed,refunded'], 'tracking_number' => ['nullable', 'string', 'max:120'], 'admin_note' => ['nullable', 'string', 'max:3000'], 'comment' => ['nullable', 'string', 'max:1000']]);

        try {
            $orderStatus->transition($order, $data['status'], [
                'actor' => $request->user(),
                'payment_status' => $data['payment_status'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'admin_note' => $data['admin_note'] ?? null,
                'comment' => $data['comment'] ?? null,
                'ip_address' => $request->ip(),
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('status', "Статус заказа {$order->number} обновлён.");
    }
}
