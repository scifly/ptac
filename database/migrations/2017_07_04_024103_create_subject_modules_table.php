<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectModulesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('subject_modules')) {
            Schema::create('subject_modules', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('subject_id')->comment('所属科目ID');
                $table->string('name', 60)->comment('科目次分类名称');
                $table->unsignedTinyInteger('weight')->comment('科目次分类权重');
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('subject_modules');
        
    }
    
}
