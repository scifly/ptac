<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePollQuestionnaireSubjectChoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('poll_questionnaire_subject_choices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pqs_id')->comment('题目ID');
			$table->string('choice')->comment('选项内容');
			$table->boolean('seq_no')->comment('选项排序编号');
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
		Schema::drop('poll_questionnaire_subject_choices');
	}

}
