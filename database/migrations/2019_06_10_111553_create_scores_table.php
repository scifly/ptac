<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scores', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('student_id')->comment('学生ID');
			$table->integer('subject_id')->comment('科目ID');
			$table->integer('exam_id')->comment('考试ID');
			$table->smallInteger('class_rank')->unsigned()->comment('班级排名');
			$table->smallInteger('grade_rank')->unsigned()->comment('年级排名');
			$table->float('score', 10, 0)->default(0)->comment('分数');
			$table->timestamps();
			$table->boolean('enabled')->comment('是否参加考试');
			$table->index(['student_id','subject_id','exam_id'], 'student_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('scores');
	}

}
