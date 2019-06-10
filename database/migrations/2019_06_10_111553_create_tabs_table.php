<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTabsTable
 */
class CreateTabsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('卡片名称');
            $table->string('comment')->comment('控制器（中文）名称');
            $table->integer('group_id')->default(0);
            $table->integer('action_id')->comment('默认加载的Action ID');
            $table->timestamps();
            $table->integer('icon_id')->nullable()->comment('图标ID');
            $table->string('remark')->nullable()->comment('卡片备注');
            $table->boolean('category')->default(0)->comment('控制器类型: 0 - 后台，1 - 微信端, 2 - 其他');
            $table->boolean('enabled');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tabs');
    }
    
}
