<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
                $table->boolean('isdefault')->comment('是否为默认的手机号码');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('mobiles');
    }
    
}
