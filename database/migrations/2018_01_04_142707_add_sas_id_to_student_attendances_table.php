<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSasIdToStudentAttendancesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('student_attendances', function (Blueprint $table) {
            $table->boolean('sas_id')->after('student_id')->nullable()->comment('关联规则id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropColumn('sas_id');
        });

    }

}
