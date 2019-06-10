<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTurnstilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('turnstiles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sn', 60)->comment('门禁标识');
			$table->boolean('doors')->comment('门的数量');
			$table->string('ip', 20);
			$table->integer('port');
			$table->integer('school_id')->comment('所属学校ID');
			$table->string('location')->comment('考勤机位置');
			$table->integer('deviceid')->comment('门禁设备id');
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
		Schema::drop('turnstiles');
	}

}
