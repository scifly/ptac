<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateGroupMenuTable
 */
class CreateGroupMenuTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('group_menu', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('group_id');
            $table->integer('menu_id');
            $table->timestamps();
            $table->boolean('enabled')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('group_menu');
    }
    
}
