<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('orders', function(Blueprint $t){$t->string('tracking_number')->nullable()->after('payment_status');$t->text('admin_note')->nullable()->after('tracking_number');$t->timestamp('cancelled_at')->nullable();$t->timestamp('stock_restored_at')->nullable();});
  Schema::create('order_status_histories', function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('from_status')->nullable();$t->string('to_status');$t->text('comment')->nullable();$t->string('ip_address',45)->nullable();$t->timestamps();$t->index(['order_id','created_at']);});
  Schema::create('payments', function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->string('provider')->default('manual');$t->string('external_id')->nullable()->unique();$t->string('type')->default('payment');$t->string('status')->default('pending');$t->unsignedBigInteger('amount');$t->string('currency',10)->default('TMT');$t->json('payload')->nullable();$t->text('note')->nullable();$t->timestamp('processed_at')->nullable();$t->timestamps();$t->index(['status','created_at']);});
 }
 public function down(): void {Schema::dropIfExists('payments');Schema::dropIfExists('order_status_histories');Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn(['tracking_number','admin_note','cancelled_at','stock_restored_at']));}
};
