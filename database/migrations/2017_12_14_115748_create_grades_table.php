<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGradesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 120)->comment('年级名称');
                $table->integer('school_id')->comment('所属学校ID');
                $table->string('educator_ids')->comment('年级主任教职员工ID');
                $table->timestamps();
                $table->boolean('enabled');
                $table->integer('department_id')->comment('对应的部门ID');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('grades');
    }
    
}
