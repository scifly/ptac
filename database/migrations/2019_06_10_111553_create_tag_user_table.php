<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTagUserTable
 */
class CreateTagUserTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tag_user', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tag_id')->comment('标签id');
            $table->integer('user_id')->comment('用户id');
            $table->timestamps();
            $table->boolean('enabled')->comment('状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tag_user');
    }
    
}
