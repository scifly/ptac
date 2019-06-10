<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateWapSiteModulesTable
 */
class CreateWapSiteModulesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('wap_site_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wap_site_id')->comment('所属微网站ID');
            $table->string('name', 60)->comment('模块名称');
            $table->integer('media_id')->comment('模块图片多媒体ID');
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
        Schema::drop('wap_site_modules');
    }
    
}
