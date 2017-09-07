<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDepartmentsTableRemoveCorpIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('corp_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('departments', function (Blueprint $table) {
            $table->integer('corp_id')->comment('所属企业ID');
        });
    }
}
