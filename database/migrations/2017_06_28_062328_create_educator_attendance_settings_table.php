<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
                $table->dateTime('start')->comment('考勤设置起始时间');
                $table->dateTime('end')->comment('考勤设置结束时间');
                $table->boolean('inorout')->comment('进或出');
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
        
        Schema::dropIfExists('educator_attendance_settings');
        
    }
    
}
