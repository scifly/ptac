<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable()->comment('父菜单ID');
                $table->string('name', 30)->comment('菜单名称');
                $table->string('remark')->nullable()->comment('菜单备注');
                $table->integer('action_id')->nullable()->comment('对应的控制器action ID');
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
        Schema::dropIfExists('menus');
    }
}
