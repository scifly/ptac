<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMajorsSubjectsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('majors_subjects')) {
            Schema::create('majors_subjects', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('major_id')->comment('专业ID');
                $table->integer('subject_id')->comment('科目ID');
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
        Schema::drop('majors_subjects');
    }
    
}
