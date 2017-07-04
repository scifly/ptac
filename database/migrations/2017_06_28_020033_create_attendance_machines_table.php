<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceMachinesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('attendance_machines')) {
            Schema::create('attendance_machines', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('考勤机名称');
                $table->string('location')->comment('考勤机位置');
                $table->integer('school_id')->comment('所属学校ID');
                $table->string('machineid', 20)->comment('考勤机id');
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
        
        Schema::dropIfExists('attendance_machines');
        
    }
}
