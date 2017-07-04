<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('group_id')->comment('所属角色/权限ID');
                $table->string('username')->comment('用户名');
                $table->string('remember_token')->comment('“记住我”令牌，登录时用');
                $table->string('password', 60)->comment('密码');
                $table->string('email')->comment('电子邮件地址');
                $table->boolean('gender')->comment('性别');
                $table->string('realname', 60)->comment('真实姓名');
                $table->string('avatar_url')->comment('头像URL');
                $table->string('wechatid')->comment('微信号');
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
        
        Schema::dropIfExists('users');
        
    }
    
}
