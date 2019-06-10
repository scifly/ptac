<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateExamTypesTable
 */
class CreateExamTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id');
            $table->string('name', 60)->comment('考试类型名称');
            $table->string('remark')->comment('考试类型备注');
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
        Schema::drop('exam_types');
    }
    
}
