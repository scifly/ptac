<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DelWechatidFieldFromUsersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'wechatid')) {
                $table->dropColumn('wechatid');
            }
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'wechatid')) {
                $table->string('wechatid')->comment('微信号');
            }
        });
    }
}
