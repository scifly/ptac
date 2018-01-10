<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameEducatorIdToUserId extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('conference_queues', function (Blueprint $table) {
            $table->renameColumn('educator_id', 'user_id')->comment('会议发起者用户id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('conference_queues', function (Blueprint $table) {
            $table->renameColumn('user_id', 'educator_id')->comment('会议发起者教职员工id');
        });
    }
}
