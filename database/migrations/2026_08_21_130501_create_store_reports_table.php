<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->text('comment')->nullable();
            $table->string('status')->default('pending'); // pending | reviewed | dismissed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_reports');
    }
};
