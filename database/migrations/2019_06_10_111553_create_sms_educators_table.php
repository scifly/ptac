<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmsEducatorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sms_educators', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('educator_id')->comment('被充值者教职员工ID');
			$table->integer('user_id')->comment('充值者用户ID');
			$table->dateTime('statistic_time')->comment('统计时间');
			$table->integer('balance')->comment('可用条数');
			$table->integer('deposit_count')->comment('充值条数');
			$table->timestamps();
			$table->boolean('enabled')->comment('类型：0-统计,1-充值');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sms_educators');
	}

}
