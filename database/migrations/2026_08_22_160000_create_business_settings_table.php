<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('business_settings', function (Blueprint $table) {
        $table->id(); $table->string('site_name')->default('AK KAGYZ'); $table->string('company_name')->nullable();
        $table->string('phone')->nullable(); $table->string('email')->nullable(); $table->string('address')->nullable(); $table->string('logo_path')->nullable();
        $table->boolean('development_mode')->default(false); $table->string('development_title')->default('Сайт временно обновляется'); $table->text('development_message')->nullable();
        $table->boolean('email_notifications')->default(true); $table->boolean('order_notifications')->default(true); $table->boolean('seller_notifications')->default(true);
        $table->boolean('otp_enabled')->default(false); $table->string('otp_channel')->default('email'); $table->unsignedSmallInteger('otp_ttl')->default(5); $table->unsignedSmallInteger('otp_length')->default(6);
        $table->boolean('smtp_enabled')->default(false); $table->string('smtp_host')->nullable(); $table->unsignedInteger('smtp_port')->default(587); $table->string('smtp_username')->nullable(); $table->text('smtp_password')->nullable(); $table->string('smtp_encryption')->default('tls'); $table->string('smtp_from_address')->nullable(); $table->string('smtp_from_name')->nullable();
        $table->timestamps();
    }); }
    public function down(): void { Schema::dropIfExists('business_settings'); }
};
