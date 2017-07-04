<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('comm_types')) {
            Schema::create('comm_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('通信方式名称');
                $table->string('remark')->comment('通信方式备注');
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
        
        Schema::dropIfExists('comm_types');
        
    }
    
}
