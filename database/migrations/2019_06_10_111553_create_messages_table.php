<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('comm_type_id')->comment('通信方式id');
			$table->integer('media_type_id')->comment('媒体类型id');
			$table->integer('app_id')->comment('应用id');
			$table->integer('msl_id')->comment('消息发送批次id');
			$table->integer('event_id')->nullable();
			$table->string('title', 64)->comment('消息标题');
			$table->text('content')->comment('消息内容');
			$table->string('serviceid')->comment('业务id');
			$table->integer('message_id')->comment('关联的消息ID');
			$table->string('url')->comment('HTML页面地址');
			$table->string('media_ids')->comment('多媒体IDs');
			$table->integer('s_user_id')->comment('发送者用户ID');
			$table->integer('r_user_id')->comment('接收者用户IDs');
			$table->integer('message_type_id')->comment('消息类型ID');
			$table->boolean('read')->comment('是否已读');
			$table->boolean('sent')->comment('消息发送是否成功');
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
		Schema::drop('messages');
	}

}
