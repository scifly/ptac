<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScoreRangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('score_ranges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 60)->comment('成绩统计项名称');
			$table->string('subject_ids')->comment('成绩统计项包含的科目IDs');
			$table->integer('school_id')->comment('成绩统计项所属学校ID');
			$table->float('start_score')->comment('成绩统计项起始分数');
			$table->float('end_score')->comment('成绩统计项截止分数');
			$table->timestamps();
			$table->boolean('enabled')->comment('是否统计');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('score_ranges');
	}

}
