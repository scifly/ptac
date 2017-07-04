<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustodiansTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('custodians')) {
            Schema::create('custodians', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('监护人用户ID');
                $table->timestamps();
                $table->dateTime('expiry')->comment('服务到期时间');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('custodians');
        
    }
    
}
