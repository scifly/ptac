<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateMobilesTable
 */
class CreateMobilesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mobiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('手机号码所属用户ID');
            $table->char('mobile', 11)->comment('手机号码');
            $table->timestamps();
            $table->boolean('enabled');
            $table->boolean('isdefault')->comment('是否为默认的手机号码');
            $table->index(['user_id', 'mobile'], 'user_id');
        });
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
