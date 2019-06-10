<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateModuleStudentTable
 */
class CreateModuleStudentTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('module_student', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('module_id')->comment('应用模块id');
            $table->integer('student_id')->comment('学生id');
            $table->integer('created_at')->nullable()->comment('创建于');
            $table->integer('updated_at')->nullable()->comment('更新于');
            $table->integer('expired_at')->nullable()->comment('到期时间');
            $table->index(['module_id', 'student_id'], 'module_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('module_student');
    }
    
}
