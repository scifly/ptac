<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableTabsRemoveActionIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->dropColumn('action_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->integer('action_id')->after('name')->comment('默认加载的Action ID');
        });
    }
}
