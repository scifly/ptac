<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollQuestionnaireAnswersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('poll_questionnaire_answers')) {
            Schema::create('poll_questionnaire_answers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('参与者用户ID');
                $table->integer('pqs_id')->commnet('调查问卷题目ID');
                $table->integer('pq_id')->comment('调查问卷ID');
                $table->string('answer')->comment('问题答案');
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
        
        Schema::dropIfExists('poll_questionnaire_answers');
        
    }
    
}
