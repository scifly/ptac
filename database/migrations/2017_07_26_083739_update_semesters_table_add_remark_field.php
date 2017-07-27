<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSemestersTableAddRemarkField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->string('remark')->nullable()->after('name')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
}
