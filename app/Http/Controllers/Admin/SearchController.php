<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
class SearchController extends Controller {
    public function __invoke(Request $request) {
        $query=trim((string)$request->input('q'));
        $type=$request->input('type');
        $type=in_array($type,['products','users','orders'],true)?$type:null;
        $safe=str_replace(['\\','%','_'],['\\\\','\%','\_'],$query);
        $like='%'.$safe.'%';
        $products=($query===''||($type&&$type!=='products'))?collect():Product::query()->where(fn($q)=>$q->where('name','like',$like)->orWhere('sku','like',$like))->latest()->limit($type?30:8)->get();
        $users=($query===''||($type&&$type!=='users'))?collect():User::query()->where(fn($q)=>$q->where('name','like',$like)->orWhere('email','like',$like)->orWhere('phone','like',$like))->latest()->limit($type?30:8)->get();
        $orders=($query===''||($type&&$type!=='orders'))?collect():Order::query()->where(fn($q)=>$q->where('number','like',$like)->orWhere('phone','like',$like))->with('user')->latest()->limit($type?30:8)->get();

        if ($request->boolean('palette')) {
            return response()->json([
                'products'=>$products->map(fn($p)=>['title'=>$p->name,'subtitle'=>'Товар · '.number_format($p->price,0,'',' ').' TMT','url'=>route('admin.products',['search'=>$p->sku?:$p->name])])->values(),
                'orders'=>$orders->map(fn($o)=>['title'=>$o->number,'subtitle'=>'Заказ · '.$o->customerName(),'url'=>route('admin.orders.show',$o)])->values(),
                'users'=>$users->map(fn($u)=>['title'=>$u->name,'subtitle'=>'Покупатель · '.$u->email,'url'=>route('admin.users',['search'=>$u->email])])->values(),
            ]);
        }

        return view('admin.search',compact('query','products','users','orders','type'));
    }
}
