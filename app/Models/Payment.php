<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model { protected $guarded=[]; protected function casts():array{return ['payload'=>'array','processed_at'=>'datetime'];} public function order(){return $this->belongsTo(Order::class);} }
