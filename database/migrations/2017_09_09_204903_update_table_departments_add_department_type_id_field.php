<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableDepartmentsAddDepartmentTypeIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('departments', function (Blueprint $table) {
            $table->integer('department_type_id')->comment('所属部门类型ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('department_type_id');
        });
    }
}
