<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateCardsTable
 */
class CreateCardsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cards', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('sn', 32)->comment('卡号');
            $table->integer('user_id')->comment('所属用户id');
            $table->timestamps();
            $table->boolean('status')->default(0)->comment('状态: 0 - 待发，1 - 正常，2 - 挂失');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cards');
    }
    
}
