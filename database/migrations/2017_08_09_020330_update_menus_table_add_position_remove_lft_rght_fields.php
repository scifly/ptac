<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMenusTableAddPositionRemoveLftRghtFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('lft');
            $table->dropColumn('rght');
            $table->integer('position')->nullable()->comment('菜单所处位置');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('lft')->nullable();
            $table->integer('rght')->nullable();
            $table->dropColumn('position');
        });
    }
}
