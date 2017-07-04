<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('content')->comment('消息内容');
                $table->string('serviceid')->comment('业务id');
                $table->integer('message_id')->comment('关联的消息ID');
                $table->string('url')->comment('HTML页面地址');
                $table->string('media_ids')->comment('多媒体IDs');
                $table->integer('user_id')->comment('发送者用户ID');
                $table->string('user_ids')->comment('接收者用户IDs');
                $table->integer('message_type_id')->comment('消息类型ID');
                $table->integer('read_count')->comment('已读数量');
                $table->integer('received_count')->comment('消息发送成功数');
                $table->integer('recipient_count')->comment('接收者数量');
                $table->timestamps();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('messages');
        
    }
    
}
