<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducatorsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('educators')) {
            Schema::create('educators', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('教职员工用户ID');
                $table->string('team_ids')->comment('所属组');
                $table->integer('school_id')->comment('所属学校ID');
                $table->integer('sms_quote')->comment('可用短信条数');
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
        
        Schema::dropIfExists('educators');
        
    }
    
}
