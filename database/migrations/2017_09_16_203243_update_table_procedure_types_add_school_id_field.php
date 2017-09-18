<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableProcedureTypesAddSchoolIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('procedure_types', function (Blueprint $table) {
            $table->integer('school_id')->comment('所属学校ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('procedure_types', function (Blueprint $table) {
            $table->dropColumn('school_id');
        });
    }
}
