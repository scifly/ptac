<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateEducatorsTable
 */
class CreateEducatorsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('educators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('教职员工用户ID');
            $table->integer('school_id')->comment('所属学校ID');
            $table->integer('sms_quote')->comment('可用短信条数');
            $table->timestamps();
            $table->boolean('enabled');
            $table->index(['user_id', 'school_id'], 'user_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('educators');
    }
    
}
