<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTeamsTableAddSchoolIdAndRemarkFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('teams', function (Blueprint $table) {
            $table->integer('school_id')->after('name')->comment('所属学校ID');
            $table->string('remark')->nullable()->after('school_id')->comment('备注');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('school_id');
            $table->dropColumn('remark');
        });
    }
}
