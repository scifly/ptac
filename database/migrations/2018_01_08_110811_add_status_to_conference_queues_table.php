<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToConferenceQueuesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('conference_queues', function (Blueprint $table) {
            if (!Schema::hasColumn('conference_queues', 'status')) {
                $table->tinyInteger('status')->comment('会议状态');
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('conference_queues', function (Blueprint $table) {
            if (Schema::hasColumn('conference_queues', 'status')) {
                $table->dropColumn('status');
            }
        });

    }
}
