<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_messages', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('msl_id')->comment('消息发送批次id');
			$table->integer('message_type_id')->comment('消息类型id');
			$table->integer('s_user_id')->comment('发送者用户id');
			$table->char('mobile', 11)->comment('手机号码');
			$table->string('content', 300)->comment('消息内容');
			$table->boolean('read')->comment('是否已读');
			$table->boolean('sent')->comment('消息是否发送成功');
			$table->timestamps();
			$table->index(['msl_id','message_type_id','s_user_id'], 'msl_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('api_messages');
	}

}
