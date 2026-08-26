<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreReport;
use App\Models\User;
class MarketplaceController extends Controller {
    public function index(){return view('admin.marketplace',['stats'=>['stores'=>User::where('role','seller')->count(),'pendingStores'=>User::where('role','seller')->where('store_status','pending')->count(),'products'=>Product::marketplace()->count(),'pendingProducts'=>Product::marketplace()->where('status','pending')->count(),'reports'=>StoreReport::where('status','pending')->count()]]);}
}
