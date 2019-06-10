<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateSubjectsTable
 */
class CreateSubjectsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->comment('所属学校ID');
            $table->string('name', 60)->comment('科目名称');
            $table->boolean('isaux')->comment('是否为副科');
            $table->smallInteger('max_score')->comment('科目满分');
            $table->smallInteger('pass_score')->comment('及格分数');
            $table->string('grade_ids')->comment('年级ID');
            $table->timestamps();
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('subjects');
    }
    
}
