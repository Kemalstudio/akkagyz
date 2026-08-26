<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class SellerTransaction extends Model {protected $guarded=[]; public function seller(){return $this->belongsTo(User::class,'seller_id');} public function orderItem(){return $this->belongsTo(OrderItem::class);} }
