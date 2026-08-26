<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderStatusService;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->withCount('items');
        $query->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
            $term = '%'.$request->string('search').'%';
            $q->where('number', 'like', $term)->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term));
        }));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $query->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')));
        $query->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at','>=',$request->date('date_from')));
        $query->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at','<=',$request->date('date_to')));
        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user','items.product.images','items.seller','payments','statusHistories.user']);
        return view('admin.orders.show', compact('order'));
    }

    public function print(Order $order)
    {
        $order->load(['user','items.seller']);
        $businessSettings = BusinessSetting::current();
        return view('admin.orders.print', compact('order', 'businessSettings'));
    }

    public function update(Request $request, Order $order, OrderStatusService $orderStatus)
    {
        $data = $request->validate(['status'=>['required','in:pending,processing,shipped,delivered,cancelled'],'payment_status'=>['nullable','in:unpaid,pending,paid,failed,refunded'],'tracking_number'=>['nullable','string','max:120'],'admin_note'=>['nullable','string','max:3000'],'comment'=>['nullable','string','max:1000']]);

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
