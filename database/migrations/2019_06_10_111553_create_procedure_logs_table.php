<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProcedureLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('procedure_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('initiator_user_id')->comment('发起人用户ID');
			$table->integer('procedure_id')->comment('流程ID');
			$table->integer('procedure_step_id')->comment('流程步骤ID');
			$table->integer('operator_user_id')->comment('操作者用户ID');
			$table->string('initiator_msg')->comment('（发起人）步骤相关留言');
			$table->string('initiator_media_ids')->comment('（发起人）步骤相关附件媒体IDs');
			$table->string('operator_msg')->comment('（操作者）步骤相关留言');
			$table->string('operator_media_ids')->comment('（操作者）步骤相关附件媒体IDs');
			$table->boolean('step_status')->comment('步骤状态：0-通过、1-拒绝、2-待定');
			$table->integer('first_log_id')->comment('该申请第一条记录的id');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('procedure_logs');
	}

}
