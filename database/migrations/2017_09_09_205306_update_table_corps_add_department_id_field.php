<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableCorpsAddDepartmentIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('corps', function (Blueprint $table) {
            $table->integer('department_id')->comment('对应的部门ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('corps', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
}
