<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateEventsTable
 */
class CreateEventsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('事件名称');
            $table->string('remark')->comment('事件备注');
            $table->string('location')->comment('时间相关地点');
            $table->string('contact', 60)->comment('事件联系人');
            $table->string('url')->comment('事件URL');
            $table->dateTime('start')->comment('事件开始时间');
            $table->dateTime('end')->comment('事件结束时间');
            $table->boolean('ispublic')->comment('事件是否公开');
            $table->boolean('iscourse')->comment('是否为课程表事件，如果是，ispublic置1');
            $table->integer('educator_id')->comment('教职员工ID，如果是课程表事件的话');
            $table->integer('subject_id')->comment('科目ID，如果是课程表事件的话');
            $table->boolean('alertable')->comment('是否提醒');
            $table->boolean('alert_mins')->comment('提醒时间(分钟)');
            $table->integer('user_id')->comment('事件创建者用户ID');
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
        Schema::drop('events');
    }
    
}
