<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersAddFieldsSetNullableFields extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('userid', 64)->comment('成员userid');
            $table->string('english_name', 64)->nullable()->comment('英文名');
            $table->string('department_ids')->comment('用户所属部门IDs');
            $table->boolean('isleader')->nullable()->comment('上级字段，标识是否为上级。第三方暂不支持');
            $table->string('position', 64)->nullable()->comment('职位信息');
            $table->string('telephone', 64)->nullable()->comment('座机号码');
            $table->integer('order')->nullable()->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面');
            $table->char('mobile', 11)->nullable()->comment('手机号码');
            $table->string('avatar_mediaid')->nullable()->comment('成员头像的mediaid，通过多媒体接口上传图片获得的mediaid');
            
            $table->string('email')->nullable()->comment('电子邮箱')->change();
            $table->string('wechatid')->nullable()->comment('微信号')->change();
            $table->string('remember_token')->nullable()->comment('"记住我"令牌，登录时用')->change();
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('userid');
            $table->dropColumn('english_name');
            $table->dropColumn('department_ids');
            $table->dropColumn('isleader');
            $table->dropColumn('position');
            $table->dropColumn('telephone');
            $table->dropColumn('order');
            $table->dropColumn('mobile');
            $table->dropColumn('avatar_mediaid');
    
            $table->string('email')->nullable(false)->comment('电子邮箱')->change();
            $table->string('wechatid')->nullable(false)->comment('微信号')->change();
            $table->string('remember_token')->nullable(false)->comment('"记住我"令牌，登录时用')->change();
        });
    }
    
}
