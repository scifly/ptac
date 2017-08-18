<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableTabsAddActionIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->integer('action_id')->after('name')->comment('默认加载的Action ID');
            $table->integer('icon_id')->nullable()->after('action_id')->comment('图标ID')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->dropColumn('action_id');
        });
    }
}
