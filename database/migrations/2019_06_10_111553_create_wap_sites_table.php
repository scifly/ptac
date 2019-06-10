<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateWapSitesTable
 */
class CreateWapSitesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('wap_sites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->comment('所属学校ID');
            $table->string('site_title')->comment('首页抬头');
            $table->string('media_ids')->comment('首页幻灯片图片多媒体ID');
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
        Schema::drop('wap_sites');
    }
    
}
