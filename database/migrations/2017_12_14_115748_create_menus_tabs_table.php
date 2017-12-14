<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTabsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('menus_tabs')) {
            Schema::create('menus_tabs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('menu_id')->comment('卡片所属菜单ID');
                $table->integer('tab_id')->comment('卡片ID');
                $table->smallInteger('tab_order')->unsigned()->nullable()->comment('卡片顺序值');
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('menus_tabs');
    }
    
}
