<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
                $table->boolean('enabled')->comment('是否启用');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('educators_classes');
    }
    
}
