<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWapSitesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('wap_sites')) {
            Schema::create('wap_sites', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('school_id')->comment('所属学校ID');
                $table->string('site_title')->comment('首页抬头');
                $table->string('media_ids')->comment('首页幻灯片图片多媒体ID');
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
        
        Schema::dropIfExists('wap_sites');
        
    }
    
}
