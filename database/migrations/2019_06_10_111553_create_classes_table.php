<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateClassesTable
 */
class CreateClassesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grade_id')->index('grade_id')->comment('所属年级ID');
            $table->integer('department_id')->index('department_id')->comment('对应的部门ID');
            $table->string('name', 120)->comment('班级名称');
            $table->string('educator_ids')->comment('班主任教职员工ID');
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
        Schema::drop('classes');
    }
    
}
