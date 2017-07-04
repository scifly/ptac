<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('考试名称');
                $table->string('remark')->comment('备注');
                $table->integer('exam_type_id')->comment('考试类型ID');
                $table->string('class_ids')->comment('参加考试的班级ID');
                $table->string('subject_ids')->comment('考试科目ID');
                $table->string('max_scores')->comment('科目满分');
                $table->string('pass_scores')->comment('科目及格分数');
                $table->date('start_date')->comment('考试开始日期');
                $table->date('end_date')->comment('考试结束日期');
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('exams');
        
    }
}
