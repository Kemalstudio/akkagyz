<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

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

    public function update(Request $request, OrderItem $orderItem)
    {
        abort_unless($orderItem->seller_id === $request->user()->id, 403);

        $request->validate(['status' => ['required', 'in:pending,shipped,delivered']]);
        $orderItem->update(['status' => $request->input('status')]);

        return back()->with('status', 'Статус заказа обновлён.');
    }
}
