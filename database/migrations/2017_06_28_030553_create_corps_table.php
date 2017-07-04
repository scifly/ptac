<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
                $table->string('corpsecret', 64)->comment('管理组的凭证密钥');
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
        
        Schema::dropIfExists('corps');
        
    }
    
}
