<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('google_play_url')->nullable()->after('support_hours');
            $table->string('app_store_url')->nullable()->after('google_play_url');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', fn (Blueprint $table) => $table->dropColumn(['google_play_url', 'app_store_url']));
    }
};
