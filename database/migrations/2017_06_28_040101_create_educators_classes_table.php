<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducatorsClassesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('educators_classes')) {
            Schema::create('educators_classes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('educator_id')->comment('教职员工ID');
                $table->integer('class_id')->comment('班级ID');
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
        
        Schema::dropIfExists('educators_classes');
        
    }
    
}
