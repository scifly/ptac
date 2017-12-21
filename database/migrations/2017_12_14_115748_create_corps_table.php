<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorpsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('corps')) {
            Schema::create('corps', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 120)->comment('企业名称');
                $table->string('corpid', 36)->comment('企业号id');
                $table->integer('company_id')->comment('所属运营者公司ID');
                $table->integer('department_id')->comment('对应的部门ID');
                $table->integer('menu_id');
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
        Schema::drop('corps');
    }
    
}
