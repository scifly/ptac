<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreatePassageLogsTable
 */
class CreatePassageLogsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('passage_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('school_id')->comment('学校id');
            $table->integer('user_id')->comment('用户id');
            $table->boolean('category')->comment('通行规则id');
            $table->boolean('direction')->comment('进出方向');
            $table->integer('turnstile_id')->comment('门禁id');
            $table->boolean('door')->comment('通行门编号: 1 - 4');
            $table->dateTime('clocked_at')->comment('打卡时间');
            $table->timestamps();
            $table->string('reason', 20)->nullable();
            $table->boolean('status')->comment('通行状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('passage_logs');
    }
    
}
