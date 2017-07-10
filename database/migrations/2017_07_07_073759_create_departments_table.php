<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('parent_id')->nullable()->comment('父部门ID');
                $table->integer('corp_id')->comment('所属企业ID');
                $table->integer('school_id')->comment('所属学校ID');
                $table->string('name', 64)->comment('部门名称');
                $table->string('remark')->nullable()->comment('部门备注');
                $table->integer('order')->nullable()->comment('在父部门中的次序值。order值大的排序靠前');
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
        
        Schema::dropIfExists('departments');
        
    }
    
}
