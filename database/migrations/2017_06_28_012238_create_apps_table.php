<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('apps')) {
            Schema::create('apps', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 36)->comment('应用名称');
                $table->string('description')->comment('应用备注');
                $table->smallInteger('agentid')->comment('应用id');
                $table->string('url')->comment('推送请求的访问协议和地址');
                $table->string('token')->comment('用于生成签名');
                $table->string('encodingaeskey')->comment('用于消息体的加密，是AES密钥的Base64编码');
                $table->boolean('report_location_flag')->comment('企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；2：持续上报');
                $table->string('logo_mediaid')->comment('企业应用头像的mediaid，通过多媒体接口上传图片获得mediaid，上传后会自动裁剪成方形和圆形两个头像');
                $table->string('redirect_domain')->comment('企业应用可信域名');
                $table->boolean('isreportuser')->comment('是否接收用户变更通知。0：不接收；1：接收。');
                $table->boolean('isreportenter')->comment('是否上报用户进入应用事件。0：不接收；1：接收。');
                $table->string('home_url')->comment('主页型应用url。url必须以http或者https开头。消息型应用无需该参数');
                $table->string('chat_extension_url')->comment('关联会话url。设置该字段后，企业会话"+"号将出现该应用，点击应用可直接跳转到此url，支持jsapi向当前会话发送消息。');
                $table->string('menu', 1024)->comment('应用菜单');
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
        
        Schema::dropIfExists('apps');
        
    }
}
