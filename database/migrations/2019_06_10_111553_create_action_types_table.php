<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('action类型名称');
			$table->string('remark')->nullable()->comment('备注');
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
		Schema::drop('action_types');
	}

}
