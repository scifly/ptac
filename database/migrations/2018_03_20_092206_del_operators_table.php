<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DelOperatorsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::drop('operators');
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (!Schema::hasTable('operators')) {
            Schema::create('operators', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('用户ID');
                $table->boolean('type')->comment('管理员类型：0 - 我们 1 - 代理人 ');
                $table->timestamps();
            });
        }
    }
}
