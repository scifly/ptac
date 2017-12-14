<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
                $table->string('remember_token')->nullable()->comment('"记住我"令牌，登录时用');
                $table->string('password', 60)->comment('密码');
                $table->string('email')->nullable()->comment('电子邮箱');
                $table->boolean('gender')->comment('性别');
                $table->string('realname', 60)->comment('真实姓名');
                $table->string('avatar_url')->comment('头像URL');
                $table->string('wechatid')->nullable()->comment('微信号');
                $table->timestamps();
                $table->boolean('enabled');
                $table->string('userid', 64)->comment('成员userid');
                $table->string('english_name', 64)->nullable()->comment('英文名');
                $table->boolean('isleader')->nullable()->comment('上级字段，标识是否为上级。第三方暂不支持');
                $table->string('position', 64)->nullable()->comment('职位信息');
                $table->string('telephone', 64)->nullable()->comment('座机号码');
                $table->integer('order')->nullable()->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面');
                $table->string('avatar_mediaid')->nullable()->comment('成员头像的mediaid，通过多媒体接口上传图片获得的mediaid');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('users');
    }
    
}
