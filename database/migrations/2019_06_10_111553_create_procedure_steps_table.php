<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProcedureStepsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('procedure_steps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('procedure_id')->comment('流程ID');
			$table->string('name', 60)->comment('流程步骤名称');
			$table->string('approver_user_ids')->comment('审批人用户IDs');
			$table->string('related_user_ids')->comment('相关人用户IDs');
			$table->string('remark')->comment('流程步骤备注');
			$table->timestamps();
			$table->boolean('enabled');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('procedure_steps');
	}

}
