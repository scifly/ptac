<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateMessageSendingLogsTable
 */
class CreateMessageSendingLogsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('message_sending_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('read_count')->comment('已读数量');
            $table->integer('received_count')->comment('消息发送成功数');
            $table->integer('recipient_count')->comment('接收者数量');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('message_sending_logs');
    }
    
}
