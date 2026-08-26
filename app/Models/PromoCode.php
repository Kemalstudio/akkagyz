<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class PromoCode extends Model{protected $guarded=[];protected function casts():array{return['is_active'=>'boolean','starts_at'=>'datetime','expires_at'=>'datetime'];}public function usages(){return $this->hasMany(PromoCodeUsage::class);}}
