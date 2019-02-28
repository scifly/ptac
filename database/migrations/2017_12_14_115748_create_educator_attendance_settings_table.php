<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEducatorAttendanceSettingsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('educator_attendance_settings')) {
            Schema::create('educator_attendance_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('考勤设置名称');
                $table->integer('school_id')->comment('考勤设置所属学校ID');
                $table->time('start')->comment('考勤设置起始时间');
                $table->time('end')->comment('考勤设置结束时间');
                $table->boolean('direction')->comment('进或出');
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
        Schema::drop('educator_attendance_settings');
    }
    
}
