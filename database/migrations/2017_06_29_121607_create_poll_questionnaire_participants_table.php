<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollQuestionnaireParticipantsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('poll_questionnaire_participants')) {
            Schema::create('poll_questionnaire_participants', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pq_id')->comment('调查问卷ID');
                $table->integer('user_id')->comment('参与者用户ID');
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
        
        Schema::dropIfExists('poll_questionnaire_participants');
        
    }
    
}
