<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromoPopup;
use App\Models\User;
class ToolsController extends Controller {
    public function index() { return view('admin.tools', ['settings'=>BusinessSetting::current(),'checks'=>['Товары без остатка'=>Product::where('stock','<=',0)->count(),'Необработанные заказы'=>Order::where('status','pending')->count(),'Заблокированные пользователи'=>User::where('is_blocked',true)->count(),'Активные popup'=>PromoPopup::active()->count(),'Товары на модерации'=>Product::where('status','pending')->count(),'Продавцы на рассмотрении'=>User::where('role','seller')->where('store_status','pending')->count()]]); }
}
