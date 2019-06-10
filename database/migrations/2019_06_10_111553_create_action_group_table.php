<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateActionGroupTable
 */
class CreateActionGroupTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('action_group', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('action_id');
            $table->integer('group_id');
            $table->timestamps();
            $table->boolean('enabled');
            $table->index(['action_id', 'group_id'], 'action_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('action_group');
    }
    
}
