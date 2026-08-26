<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class SellerPayout extends Model {protected $guarded=[];protected function casts():array{return ['processed_at'=>'datetime'];}public function seller(){return $this->belongsTo(User::class,'seller_id');}public function processor(){return $this->belongsTo(User::class,'processed_by');}}
