<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTabsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::create('menus_tabs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->comment('卡片所属菜单ID');
            $table->integer('tab_id')->comment('卡片ID');
            $table->unsignedSmallInteger('tab_order')->nullable()->comment('卡片顺序值');
            $table->timestamps();
            $table->boolean('enabled');
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('menus_tabs');
        
    }
    
}
