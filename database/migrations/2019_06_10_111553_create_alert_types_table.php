<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlertTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alert_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('提前提醒的时间');
			$table->string('english_name', 60)->comment('提前提醒时间的英文名称');
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
		Schema::drop('alert_types');
	}

}
