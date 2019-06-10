<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateProcedureTypesTable
 */
class CreateProcedureTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('procedure_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('流程种类名称');
            $table->string('remark')->comment('流程种类备注');
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
        Schema::drop('procedure_types');
    }
    
}
