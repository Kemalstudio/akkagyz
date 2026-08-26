<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('business_settings', function (Blueprint $table) {
        $table->string('tagline')->nullable()->after('site_name');
        $table->text('store_description')->nullable()->after('company_name');
        $table->string('currency', 10)->default('TMT')->after('address');
        $table->string('primary_color', 7)->default('#285ED6')->after('currency');
        $table->string('instagram_url')->nullable()->after('primary_color');
        $table->string('telegram_url')->nullable()->after('instagram_url');
        $table->string('whatsapp_url')->nullable()->after('telegram_url');
        $table->string('support_hours')->nullable()->after('whatsapp_url');
    }); }
    public function down(): void { Schema::table('business_settings', fn (Blueprint $table) => $table->dropColumn(['tagline','store_description','currency','primary_color','instagram_url','telegram_url','whatsapp_url','support_hours'])); }
};
