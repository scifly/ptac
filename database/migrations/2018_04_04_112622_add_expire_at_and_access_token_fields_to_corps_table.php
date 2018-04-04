<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddExpireAtAndAccessTokenFieldsToCorpsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        Schema::table('corps', function (Blueprint $table) {
            if (!Schema::hasColumn('corps', 'access_token')) {
                $table->string('access_token')->nullable()->after('menu_id')->comment('通讯录同步应用access_token');
            }
            if (!Schema::hasColumn('corps', 'expire_at')) {
                $table->dateTime('expire_at')->nullable()->after('updated_at')->comment('通讯录同步应用access_token到期时间');
            }
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('corps', function (Blueprint $table) {
            if (Schema::hasColumn('corps', 'access_token')) {
                $table->dropColumn('access_token');
            }
            if (Schema::hasColumn('corps', 'expire_at')) {
                $table->dropColumn('expire_at');
            }
        });
        
    }
}
