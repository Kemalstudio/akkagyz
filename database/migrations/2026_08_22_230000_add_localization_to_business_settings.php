<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::table('business_settings',function(Blueprint $t){$t->string('default_locale',5)->default('ru');$t->json('enabled_locales')->nullable();});}public function down():void{Schema::table('business_settings',fn(Blueprint $t)=>$t->dropColumn(['default_locale','enabled_locales']));}};
