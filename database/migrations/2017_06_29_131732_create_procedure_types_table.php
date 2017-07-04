<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('procedure_types')) {
            Schema::create('procedure_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('流程种类名称');
                $table->string('remark')->comment('流程种类备注');
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
        
        Schema::dropIfExists('procedure_types');
        
    }
    
}
