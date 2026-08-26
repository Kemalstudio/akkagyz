<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class SupportMessage extends Model{protected $guarded=[];public function user(){return $this->belongsTo(User::class);}public function ticket(){return $this->belongsTo(SupportTicket::class,'support_ticket_id');}}
