<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsMenusTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('groups_menus')) {
            Schema::create('groups_menus', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('group_id');
                $table->integer('menu_id');
                $table->timestamps();
                $table->boolean('enabled')->nullable();
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('groups_menus');
    }
    
}
