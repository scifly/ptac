<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableGroupsMenus extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('groups_menus', function (Blueprint $table) {
            $table->integer('group_id')->comment('角色ID');
            $table->integer('menu_id')->comment('菜单ID');
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('groups_menus', function (Blueprint $table) {
            $table->dropColumn('group_id');
            $table->dropColumn('menu_id');
            $table->dropColumn('enabled');
        });
    }
}
