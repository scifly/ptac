<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePollQuestionnairesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('poll_questionnaires', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('school_id')->comment('所属学校ID');
			$table->integer('user_id')->comment('发起者用户ID');
			$table->string('name')->comment('问卷调查名称');
			$table->dateTime('start')->comment('开始时间');
			$table->dateTime('end')->comment('结束时间');
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
		Schema::drop('poll_questionnaires');
	}

}
