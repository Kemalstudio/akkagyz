<?php
namespace App\Services;
use App\Models\BusinessSetting; use App\Models\Order; use App\Models\OrderItem; use App\Models\SellerTransaction; use Illuminate\Support\Facades\DB;
class MarketplaceFinanceService {
 public function accrue(Order $order):void { $rate=(float)(BusinessSetting::current()->platform_commission_percent??10); foreach($order->items()->whereNotNull('seller_id')->get() as $item){$gross=$item->price*$item->quantity;$commission=(int)round($gross*$rate/100);SellerTransaction::firstOrCreate(['order_item_id'=>$item->id,'type'=>'sale'],['seller_id'=>$item->seller_id,'gross_amount'=>$gross,'commission_amount'=>$commission,'net_amount'=>$gross-$commission,'status'=>'available','description'=>'Продажа по заказу '.$order->number]);}}
 public function reverse(Order $order):void {foreach($order->items()->whereNotNull('seller_id')->get() as $item){$sale=SellerTransaction::where('order_item_id',$item->id)->where('type','sale')->first();if($sale)SellerTransaction::firstOrCreate(['order_item_id'=>$item->id,'type'=>'refund'],['seller_id'=>$item->seller_id,'gross_amount'=>-$sale->gross_amount,'commission_amount'=>-$sale->commission_amount,'net_amount'=>-$sale->net_amount,'status'=>'available','description'=>'Отмена заказа '.$order->number]);}}
 /** Возврат средств по одной позиции заказа (например, по заявке на возврат) — уменьшает баланс продавца пропорционально сумме возврата. */
 public function reverseItem(OrderItem $item,int $refundAmount,string $description):void {
  if(!$item->seller_id||$refundAmount<=0) return;
  $sale=SellerTransaction::where('order_item_id',$item->id)->where('type','sale')->first();
  if(!$sale||$sale->gross_amount<=0) return;
  $ratio=min(1,$refundAmount/$sale->gross_amount);
  $grossReversed=(int)round($sale->gross_amount*$ratio);
  $commissionReversed=(int)round($sale->commission_amount*$ratio);
  SellerTransaction::firstOrCreate(['order_item_id'=>$item->id,'type'=>'refund'],['seller_id'=>$item->seller_id,'gross_amount'=>-$grossReversed,'commission_amount'=>-$commissionReversed,'net_amount'=>-($grossReversed-$commissionReversed),'status'=>'available','description'=>$description]);
 }
 public function balance(int $sellerId):int{return (int)SellerTransaction::where('seller_id',$sellerId)->where('status','available')->lockForUpdate()->sum('net_amount');}
}
