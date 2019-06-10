<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comm_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('通信方式名称');
			$table->string('remark')->comment('通信方式备注');
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
		Schema::drop('comm_types');
	}

}
