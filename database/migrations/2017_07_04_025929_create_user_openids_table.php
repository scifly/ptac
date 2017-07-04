<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOpenidsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('user_openids')) {
            Schema::create('user_openids', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('用户ID');
                $table->integer('corp_id')->comment('企业ID');
                $table->string('openid', 28)->comment('用户微信openid');
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
        
        Schema::dropIfExists('user_openids');
        
    }
    
}
