<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConferenceParticipantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('conference_participants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('educator_id')->comment('与会者教职员工ID');
			$table->dateTime('attendance_time')->comment('与会者签到时间');
			$table->integer('conference_queue_id')->comment('会议队列ID');
			$table->timestamps();
			$table->boolean('status')->comment('状态（0 - 签到已到 1 - 签到未到）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('conference_participants');
	}

}
