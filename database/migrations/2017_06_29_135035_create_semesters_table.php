<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSemestersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('semesters')) {
            Schema::create('semesters', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('school_id')->comment('所属学校ID');
                $table->string('name', 60)->comment('学期名称');
                $table->date('start_date')->comment('学期开始日期');
                $table->date('end_date')->comment('学期截止日期');
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
        
        Schema::dropIfExists('semesters');
        
    }
    
}
