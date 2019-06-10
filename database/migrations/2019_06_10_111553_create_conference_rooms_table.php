<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateConferenceRoomsTable
 */
class CreateConferenceRoomsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('conference_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('会议室名称');
            $table->integer('school_id')->comment('会议室所属学校ID');
            $table->smallInteger('capacity')->comment('会议室容量');
            $table->string('remark')->comment('会议室备注');
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
        Schema::drop('conference_rooms');
    }
    
}
