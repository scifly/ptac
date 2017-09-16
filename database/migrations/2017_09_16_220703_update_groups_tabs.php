<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGroupsTabs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('groups_tabs', function (Blueprint $table) {
            $table->integer('group_id')->comment('角色ID');
            $table->integer('tab_id')->comment('卡片ID');
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('groups_tabs', function (Blueprint $table) {
            $table->dropColumn('group_id');
            $table->dropColumn('tab_id');
            $table->dropColumn('enabled');
        });
    }
}
