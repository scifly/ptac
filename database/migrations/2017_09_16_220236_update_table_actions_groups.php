<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableActionsGroups extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('actions_groups', function (Blueprint $table) {
            $table->integer('action_id')->comment('功能ID');
            $table->integer('group_id')->comment('角色ID');
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('actions_groups', function (Blueprint $table) {
            $table->dropColumn('action_id');
            $table->dropColumn('group_id');
            $table->dropColumn('enabled');
        });
    }
}
