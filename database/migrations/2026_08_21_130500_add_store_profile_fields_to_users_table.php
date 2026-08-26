<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_slug')->nullable()->unique()->after('store_name');
            $table->string('store_address')->nullable()->after('store_description');
            $table->boolean('is_vip')->default(false)->after('is_blocked');
            $table->timestamp('vip_activated_at')->nullable()->after('is_vip');
            $table->unsignedInteger('store_views')->default(0)->after('vip_activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['store_slug', 'store_address', 'is_vip', 'vip_activated_at', 'store_views']);
        });
    }
};
