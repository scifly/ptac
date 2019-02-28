<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentAttendancesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('student_attendances')) {
            Schema::create('student_attendances', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('student_id')->comment('学生ID');
                $table->dateTime('punch_time')->comment('打卡时间');
                $table->boolean('direction')->comment('进或出');
                $table->integer('attendance_machine_id')->comment('考勤机ID');
                $table->integer('media_id')->comment('考勤照片多媒体ID');
                $table->float('longitude')->comment('打卡时所处经度');
                $table->float('latitude')->comment('打卡时所处纬度');
                $table->timestamps();
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('student_attendances');
    }
    
}
