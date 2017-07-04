<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobilesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('mobiles')) {
            Schema::create('mobiles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->comment('手机号码所属用户ID');
                $table->char('mobile', 11)->comment('手机号码');
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
        
        Schema::dropIfExists('mobiles');
        
    }
    
}
