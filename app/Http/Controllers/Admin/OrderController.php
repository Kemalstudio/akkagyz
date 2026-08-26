<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\MarketplaceFinanceService;

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
        return view('admin.orders.print', compact('order'));
    }

    public function update(Request $request, Order $order, MarketplaceFinanceService $finance)
    {
        $data = $request->validate(['status'=>['required','in:pending,processing,shipped,delivered,cancelled'],'payment_status'=>['nullable','in:unpaid,pending,paid,failed,refunded'],'tracking_number'=>['nullable','string','max:120'],'admin_note'=>['nullable','string','max:3000'],'comment'=>['nullable','string','max:1000']]);
        DB::transaction(function () use ($order,$data,$request,$finance) {
            $locked=Order::whereKey($order->id)->lockForUpdate()->firstOrFail(); $old=$locked->status;
            if($data['status']==='cancelled' && !$locked->stock_restored_at){
                foreach($locked->items()->lockForUpdate()->get() as $item){if($item->product_id) \App\Models\Product::whereKey($item->product_id)->increment('stock',$item->quantity);$item->update(['status'=>'cancelled']);}
                $locked->stock_restored_at=now();$locked->cancelled_at=now();$finance->reverse($locked);
            } elseif($data['status']!=='cancelled') {
                $itemStatus=match($data['status']){'pending'=>'pending','processing'=>'pending','shipped'=>'shipped','delivered'=>'delivered',default=>'pending'};
                $locked->items()->where('status','!=','cancelled')->update(['status'=>$itemStatus]);
            }
            $locked->fill(['status'=>$data['status'],'tracking_number'=>$data['tracking_number']??$locked->tracking_number,'admin_note'=>$data['admin_note']??$locked->admin_note]);
            if(isset($data['payment_status']))$locked->payment_status=$data['payment_status']; $locked->save();
            if($locked->status==='delivered') $finance->accrue($locked);
            if($old!==$locked->status || !empty($data['comment'])) $locked->statusHistories()->create(['user_id'=>$request->user()->id,'from_status'=>$old,'to_status'=>$locked->status,'comment'=>$data['comment']??null,'ip_address'=>$request->ip()]);
            if(isset($data['payment_status']) && $data['payment_status']==='paid' && !$locked->payments()->where('status','succeeded')->exists()) $locked->payments()->create(['status'=>'succeeded','amount'=>$locked->total,'currency'=>\App\Models\BusinessSetting::current()->currency?:'TMT','note'=>'Подтверждено администратором','processed_at'=>now()]);
        });

        return back()->with('status', "Статус заказа {$order->number} обновлён.");
    }
}
