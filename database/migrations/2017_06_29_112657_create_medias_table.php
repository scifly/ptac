<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('medias')) {
            Schema::create('medias', function (Blueprint $table) {
                $table->increments('id');
                $table->string('path')->comment('媒体文件路径');
                $table->string('remark')->comment('媒体文件备注');
                $table->integer('media_type_id')->comment('媒体类型ID');
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
        
        Schema::dropIfExists('medias');
        
    }
    
}
