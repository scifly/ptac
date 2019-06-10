<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateConferenceQueuesTable
 */
class CreateConferenceQueuesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('conference_queues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 120)->comment('会议名称');
            $table->string('remark')->comment('会议备注');
            $table->dateTime('start')->comment('会议开始时间');
            $table->dateTime('end')->comment('会议结束时间');
            $table->integer('user_id')->comment('发起人教职员工ID');
            $table->string('educator_ids')->comment('（应到）与会者教职员工ID');
            $table->string('attended_educator_ids')->comment('（应到）与会者教职员工ID');
            $table->integer('conference_room_id')->comment('会议室ID');
            $table->string('attendance_qrcode_url')->comment('扫码签到用二维码URL');
            $table->integer('event_id')->comment('相关日程ID');
            $table->timestamps();
            $table->boolean('status')->comment('会议状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('conference_queues');
    }
    
}
