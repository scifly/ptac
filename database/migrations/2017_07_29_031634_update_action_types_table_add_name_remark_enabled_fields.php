<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActionTypesTableAddNameRemarkEnabledFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_types', function (Blueprint $table) {
            $table->string('name', 60)->comment('action类型名称');
            $table->string('remark')->nullable()->comment('备注');
            $table->boolean('enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action_types', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('remark');
            $table->dropColumn('enabled');
        });
    }
}
