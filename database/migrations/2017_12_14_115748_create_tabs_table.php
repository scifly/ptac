<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTabsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('tabs')) {
            Schema::create('tabs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment('卡片名称');
                $table->integer('group_id')->default(0);
                $table->integer('action_id')->comment('默认加载的Action ID');
                $table->string('remark')->nullable()->comment('卡片备注');
                $table->timestamps();
                $table->boolean('enabled');
                $table->integer('icon_id')->nullable()->comment('图标ID');
                $table->string('controller')->comment('控制器名称');
                $table->integer('new_column')->nullable();
            });
        }
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
