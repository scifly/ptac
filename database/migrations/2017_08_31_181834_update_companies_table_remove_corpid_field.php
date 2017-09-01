<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCompaniesTableRemoveCorpidField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('corpid');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('corpid', 48)->comment('运营者公司对应的企业号');
        });
    }
}
