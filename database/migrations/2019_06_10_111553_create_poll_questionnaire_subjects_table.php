<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePollQuestionnaireSubjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('poll_questionnaire_subjects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('subject')->comment('题目名称');
			$table->integer('pq_id')->comment('调查问卷ID');
			$table->boolean('subject_type')->comment('题目类型：0 - 单选，1 - 多选, 2 - 填空');
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
		Schema::drop('poll_questionnaire_subjects');
	}

}
