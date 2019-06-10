<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMajorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('majors', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('专业名称');
			$table->string('remark')->comment('专业备注');
			$table->integer('school_id')->comment('所属学校ID');
			$table->timestamps();
			$table->string('enabled');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('majors');
	}

}
