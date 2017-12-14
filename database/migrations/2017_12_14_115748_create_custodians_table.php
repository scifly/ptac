<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('custodians');
    }
    
}
