<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreTotalsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('score_totals')) {
            Schema::create('score_totals', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('student_id')->comment('学生ID');
                $table->integer('exam_id')->comment('考试ID');
                $table->float('score')->comment('总分');
                $table->string('subject_ids')->comment('计入总成绩的科目IDs');
                $table->string('na_subject_ids')->comment('未计入总成绩的科目IDs');
                $table->unsignedSmallInteger('class_rank')->comment('班级排名');
                $table->unsignedSmallInteger('grade_rank')->comment('年级排名');
                $table->timestamps();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('score_totals');
        
    }
    
}
