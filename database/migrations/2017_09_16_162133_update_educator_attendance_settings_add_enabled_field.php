<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEducatorAttendanceSettingsAddEnabledField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('educator_attendance_settings', function (Blueprint $table) {
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('educator_attendance_settings', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
