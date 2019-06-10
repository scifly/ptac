<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePollQuestionnaireParticipantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('poll_questionnaire_participants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pq_id')->comment('调查问卷ID');
			$table->integer('user_id')->comment('参与者用户ID');
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
		Schema::drop('poll_questionnaire_participants');
	}

}
