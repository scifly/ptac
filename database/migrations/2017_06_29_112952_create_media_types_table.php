<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('media_types')) {
            Schema::create('media_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('媒体类型名称');
                $table->string('remark')->comment('媒体类型备注');
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
        
        Schema::dropIfExists('media_types');
        
    }
    
}
