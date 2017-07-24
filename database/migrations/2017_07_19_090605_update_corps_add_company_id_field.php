<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorpsAddCompanyIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('corps', function (Blueprint $table){
            $table->integer('company_id')->comment('所属运营者公司ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corps', function (Blueprint $table){
            $table->dropColumn('company_id');
        });
    }
}
