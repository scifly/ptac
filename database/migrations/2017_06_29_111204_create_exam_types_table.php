<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('exam_types')) {
            Schema::create('exam_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('考试类型名称');
                $table->string('remark')->comment('考试类型备注');
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
        
        Schema::dropIfExists('exam_types');
        
    }
    
}
