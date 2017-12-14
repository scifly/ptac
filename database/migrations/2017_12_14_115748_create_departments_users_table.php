<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentsUsersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('departments_users')) {
            Schema::create('departments_users', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('department_id')->comment('部门ID');
                $table->integer('user_id')->comment('用户ID');
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
        Schema::drop('departments_users');
    }
    
}
