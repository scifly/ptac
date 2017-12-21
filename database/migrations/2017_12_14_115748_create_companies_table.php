<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('department_id');
                $table->integer('menu_id');
                $table->string('name', 120)->comment('运营者公司名称');
                $table->string('remark')->comment('运营者公司备注');
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('companies');
    }
    
}
