<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWechatSmsesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wechat_smses', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('urlcode')->unique('wechat_smses_urlcode_uindex')->comment('消息详情代码');
			$table->integer('message_id')->comment('消息id');
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
		Schema::drop('wechat_smses');
	}

}
