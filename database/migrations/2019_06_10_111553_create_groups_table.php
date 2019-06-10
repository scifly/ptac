<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateGroupsTable
 */
class CreateGroupsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('角色名称');
            $table->integer('school_id')->nullable();
            $table->string('remark')->comment('角色备注');
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
        Schema::drop('groups');
    }
    
}
