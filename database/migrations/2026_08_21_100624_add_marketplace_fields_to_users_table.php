<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password'); // customer | seller | admin
            $table->string('store_name')->nullable()->after('role');
            $table->string('store_status')->nullable()->after('store_name'); // pending | approved | rejected
            $table->text('store_description')->nullable()->after('store_status');
            $table->timestamp('store_approved_at')->nullable()->after('store_description');
            $table->string('phone')->nullable()->after('store_approved_at');
            $table->boolean('is_blocked')->default(false)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'store_name', 'store_status', 'store_description', 'store_approved_at', 'phone', 'is_blocked']);
        });
    }
};
