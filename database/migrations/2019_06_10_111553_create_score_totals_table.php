<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScoreTotalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('score_totals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('student_id')->comment('学生ID');
			$table->integer('exam_id')->comment('考试ID');
			$table->float('score')->comment('总分');
			$table->string('subject_ids')->comment('计入总成绩的科目IDs');
			$table->string('na_subject_ids')->comment('未计入总成绩的科目IDs');
			$table->smallInteger('class_rank')->unsigned()->comment('班级排名');
			$table->smallInteger('grade_rank')->unsigned()->comment('年级排名');
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
		Schema::drop('score_totals');
	}

}
