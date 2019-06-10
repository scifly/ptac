<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateGroupTabTable
 */
class CreateGroupTabTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('group_tab', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('group_id');
            $table->integer('tab_id');
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
        Schema::drop('group_tab');
    }
    
}
