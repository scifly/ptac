<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionsTableAllowRemarkFieldToBeNull extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('remark')->nullable()->comment('备注')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('remark')->nullable(false)->comment('备注')->change();
        });
    }
}
