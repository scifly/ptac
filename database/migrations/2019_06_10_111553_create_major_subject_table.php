<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateMajorSubjectTable
 */
class CreateMajorSubjectTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('major_subject', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('major_id')->comment('专业ID');
            $table->integer('subject_id')->comment('科目ID');
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
        Schema::drop('major_subject');
    }
    
}
