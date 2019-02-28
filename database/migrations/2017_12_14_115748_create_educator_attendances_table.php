<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEducatorAttendancesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('educator_attendances')) {
            Schema::create('educator_attendances', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('educator_id')->comment('教职员工ID');
                $table->dateTime('punch_time')->comment('打卡日期时间');
                $table->float('longitude')->comment('签到时所处经度');
                $table->float('latitude')->comment('签到时所处纬度');
                $table->boolean('direction')->comment('进或出');
                $table->integer('eas_id')->comment('所属考勤设置ID');
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
        Schema::drop('educator_attendances');
    }
    
}
