<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateTagsTable
 */
class CreateTagsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('教职员工组名称');
            $table->integer('school_id')->comment('所属学校ID');
            $table->integer('user_id')->comment('创建者的用户id');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->boolean('enabled');
            $table->boolean('synced')->comment('同步状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tags');
    }
    
}
