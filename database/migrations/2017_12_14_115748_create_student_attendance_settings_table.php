<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentAttendanceSettingsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('student_attendance_settings')) {
            Schema::create('student_attendance_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('学生考勤设置名称');
                $table->integer('grade_id')->comment('所属年级ID');
                $table->integer('semester_id')->comment('学期ID');
                $table->boolean('ispublic')->comment('是否为学校公用考勤设置');
                $table->time('start')->comment('考勤时段起始时间');
                $table->time('end')->comment('考勤时段截止时间');
                $table->string('day', 10)->comment('星期几？');
                $table->timestamps();
                $table->string('msg_template')->comment('考勤消息模板');
                $table->boolean('direction')->comment('进或出');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('student_attendance_settings');
    }
    
}
