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
        $like='%'.$query.'%';
        $products=$query===''?collect():Product::query()->where(fn($q)=>$q->where('name','like',$like)->orWhere('sku','like',$like))->latest()->limit(8)->get();
        $users=$query===''?collect():User::query()->where(fn($q)=>$q->where('name','like',$like)->orWhere('email','like',$like)->orWhere('phone','like',$like))->latest()->limit(8)->get();
        $orders=$query===''?collect():Order::query()->where(fn($q)=>$q->where('number','like',$like)->orWhere('phone','like',$like))->with('user')->latest()->limit(8)->get();
        return view('admin.search',compact('query','products','users','orders'));
    }
}
