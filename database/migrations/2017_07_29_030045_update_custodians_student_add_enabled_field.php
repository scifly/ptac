<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCustodiansStudentAddEnabledField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custodians_students', function (Blueprint $table) {
            $table->boolean('enabled')->after('updated_at')->comment('是否启用');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custodians_students', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
