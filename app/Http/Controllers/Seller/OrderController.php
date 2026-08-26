<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orderItems = OrderItem::with(['order.user', 'product'])
            ->where('seller_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('seller.orders', compact('orderItems'));
    }

    public function update(Request $request, OrderItem $orderItem, OrderStatusService $orderStatus)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,shipped,delivered']]);

        try {
            $orderStatus->updateItemStatus($orderItem, $data['status'], $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('status', 'Статус заказа обновлён.');
    }
}
