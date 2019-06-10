<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIconsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('icons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('图标的css类名');
			$table->integer('icon_type_id')->comment('所属图标类型ID');
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
		Schema::drop('icons');
	}

}
