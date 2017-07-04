<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollQuestionnaireSubjectChoicesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('poll_questionnaire_subject_choices')) {
            Schema::create('poll_questionnaire_subject_choices', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pqs_id')->comment('题目ID');
                $table->string('choice')->comment('选项内容');
                $table->unsignedTinyInteger('seq_no')->comment('选项排序编号');
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
        
        Schema::dropIfExists('poll_questionnaire_subject_choices');
        
    }
    
}
