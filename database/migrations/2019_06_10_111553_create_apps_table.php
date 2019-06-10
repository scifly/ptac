<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateAppsTable
 */
class CreateAppsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('corp_id')->comment('所属企业id');
            $table->string('name', 36)->comment('应用名称');
            $table->string('secret', 60)->comment('应用Secret');
            $table->string('description')->comment('应用备注');
            $table->string('agentid', 10)->comment('应用id');
            $table->boolean('report_location_flag')->comment('企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；2：持续上报');
            $table->string('square_logo_url')->comment('企业应用方形头像');
            $table->string('redirect_domain')->comment('企业应用可信域名');
            $table->boolean('isreportenter')->comment('是否上报用户进入应用事件。0：不接收；1：接收。');
            $table->string('home_url')->comment('主页型应用url。url必须以http或者https开头。消息型应用无需该参数');
            $table->string('menu', 1024)->comment('应用菜单');
            $table->string('allow_userinfos', 2048)->comment('企业应用可见范围（人员），其中包括userid');
            $table->string('allow_partys', 1024)->comment('企业应用可见范围（部门）');
            $table->string('allow_tags')->comment('企业应用可见范围（标签）');
            $table->string('access_token')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->timestamps();
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('apps');
    }
    
}
