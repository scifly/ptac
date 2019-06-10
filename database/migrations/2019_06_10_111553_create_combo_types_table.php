<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComboTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('combo_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('套餐名称');
			$table->integer('amount')->comment('套餐金额');
			$table->smallInteger('discount')->comment('折扣比例(80,90)');
			$table->integer('school_id')->comment('套餐所属学校ID');
			$table->boolean('months')->comment('有效月数');
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
		Schema::drop('combo_types');
	}

}
