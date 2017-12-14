<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('message_types')) {
            Schema::create('message_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60)->comment('消息类型名称');
                $table->string('remark')->comment('消息类型备注');
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
        Schema::drop('message_types');
    }
    
}
