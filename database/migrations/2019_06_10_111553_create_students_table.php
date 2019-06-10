<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateStudentsTable
 */
class CreateStudentsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('class_id')->comment('班级ID');
            $table->string('sn', 32)->comment('学号');
            $table->dateTime('birthday')->comment('生日');
            $table->string('remark')->comment('备注');
            $table->timestamps();
            $table->boolean('oncampus')->comment('是否住校');
            $table->boolean('enabled');
            $table->index(['user_id', 'class_id'], 'user_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('students');
    }
    
}
