<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('storefront.orders', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items.product');

        return view('storefront.order-show', compact('order'));
    }
}
