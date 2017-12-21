<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTabsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('groups_tabs')) {
            Schema::create('groups_tabs', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('group_id');
                $table->integer('tab_id');
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
        Schema::drop('groups_tabs');
    }
    
}
