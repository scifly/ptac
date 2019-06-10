<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateDepartmentTagTable
 */
class CreateDepartmentTagTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('department_tag', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('department_id')->comment('部门id');
            $table->integer('tag_id')->comment('标签id');
            $table->timestamps();
            $table->boolean('enabled')->comment('状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('department_tag');
    }
    
}
