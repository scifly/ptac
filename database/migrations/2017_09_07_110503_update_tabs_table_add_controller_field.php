<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTabsTableAddControllerField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->string('controller')->comment('控制器名称');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->dropColumn('controller');
        });
    }
}
