<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsmArticlesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('wsm_articles')) {
            Schema::create('wsm_articles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('wsm_id')->comment('所属网站模块ID');
                $table->string('name', 120)->comment('文章名称');
                $table->string('summary')->comment('文章摘要');
                $table->integer('thumbnail_media_id')->comment('缩略图多媒体ID');
                $table->text('content')->comment('文章内容');
                $table->string('media_ids')->comment('附件多媒体ID');
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
        
        Schema::dropIfExists('wsm_articles');
        
    }
    
}
