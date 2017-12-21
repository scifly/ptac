<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionsGroupsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('actions_groups')) {
            Schema::create('actions_groups', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('action_id');
                $table->integer('group_id');
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
        Schema::drop('actions_groups');
    }
    
}
