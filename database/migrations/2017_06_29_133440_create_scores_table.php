<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('scores')) {
            Schema::create('scores', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('student_id')->comment('学生ID');
                $table->integer('subject_id')->comment('科目ID');
                $table->integer('exam_id')->comment('考试ID');
                $table->unsignedSmallInteger('class_rank')->comment('班级排名');
                $table->unsignedSmallInteger('grade_rank')->comment('年级排名');
                $table->float('score')->comment('分数');
                $table->timestamps();
                $table->boolean('enabled')->comment('是否参加考试');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('scores');
        
    }
    
}
