<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateProceduresTable
 */
class CreateProceduresTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('procedure_type_id')->comment('流程类型ID');
            $table->integer('school_id')->comment('流程所属学校ID');
            $table->string('name', 60)->comment('流程名称');
            $table->string('remark')->comment('流程备注');
            $table->timestamps();
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('procedures');
    }
    
}
