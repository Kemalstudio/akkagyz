<?php
namespace App\Services;
use App\Models\BusinessSetting; use App\Models\Order; use App\Models\SellerTransaction; use Illuminate\Support\Facades\DB;
class MarketplaceFinanceService {
 public function accrue(Order $order):void { $rate=(float)(BusinessSetting::current()->platform_commission_percent??10); foreach($order->items()->whereNotNull('seller_id')->get() as $item){$gross=$item->price*$item->quantity;$commission=(int)round($gross*$rate/100);SellerTransaction::firstOrCreate(['order_item_id'=>$item->id,'type'=>'sale'],['seller_id'=>$item->seller_id,'gross_amount'=>$gross,'commission_amount'=>$commission,'net_amount'=>$gross-$commission,'status'=>'available','description'=>'Продажа по заказу '.$order->number]);}}
 public function reverse(Order $order):void {foreach($order->items()->whereNotNull('seller_id')->get() as $item){$sale=SellerTransaction::where('order_item_id',$item->id)->where('type','sale')->first();if($sale)SellerTransaction::firstOrCreate(['order_item_id'=>$item->id,'type'=>'refund'],['seller_id'=>$item->seller_id,'gross_amount'=>-$sale->gross_amount,'commission_amount'=>-$sale->commission_amount,'net_amount'=>-$sale->net_amount,'status'=>'available','description'=>'Отмена заказа '.$order->number]);}}
 public function balance(int $sellerId):int{return (int)SellerTransaction::where('seller_id',$sellerId)->where('status','available')->sum('net_amount');}
}
