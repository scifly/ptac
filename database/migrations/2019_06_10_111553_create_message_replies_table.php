<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageRepliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_replies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('msl_id')->comment('所属消息批次');
			$table->integer('user_id')->comment('消息回复者id');
			$table->string('content')->comment('回复内容');
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
		Schema::drop('message_replies');
	}

}
