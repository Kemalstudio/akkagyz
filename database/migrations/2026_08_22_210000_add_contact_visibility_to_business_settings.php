<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->boolean('contact_button_enabled')->default(true)->after('phone');
            $table->boolean('contact_phone_visible')->default(true)->after('contact_button_enabled');
        });

        DB::table('business_settings')->update(['phone' => '+99364005374']);
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn(['contact_button_enabled', 'contact_phone_visible']);
        });
    }
};
