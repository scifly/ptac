<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateDepartmentsTable
 */
class CreateDepartmentsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable()->comment('父部门ID');
            $table->integer('department_type_id')->comment('所属部门类型ID');
            $table->string('name', 64)->comment('部门名称');
            $table->string('remark')->nullable()->comment('部门备注');
            $table->integer('order')->nullable()->comment('在父部门中的次序值。order值大的排序靠前');
            $table->timestamps();
            $table->boolean('enabled');
            $table->boolean('synced')->default(0)->comment('是否已同步到企业微信通讯录');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('departments');
    }
    
}
