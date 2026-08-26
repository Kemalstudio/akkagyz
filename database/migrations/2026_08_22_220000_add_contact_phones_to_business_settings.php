<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('business_settings', fn(Blueprint $table) => $table->json('contact_phones')->nullable()->after('phone'));
        DB::table('business_settings')->orderBy('id')->each(function($settings){ if($settings->phone) DB::table('business_settings')->where('id',$settings->id)->update(['contact_phones'=>json_encode([$settings->phone],JSON_UNESCAPED_UNICODE)]); });
    }
    public function down(): void { Schema::table('business_settings',fn(Blueprint $table)=>$table->dropColumn('contact_phones')); }
};
