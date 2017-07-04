<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperatorsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('operators')) {
            Schema::create('operators', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->comment('所属运营者公司ID');
                $table->integer('user_id')->comment('用户ID');
                $table->string('school_ids')->comment('可管理的学校ID');
                $table->boolean('type')->comment('管理员类型：0 - 我们 1 - 代理人 ');
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
        
        Schema::dropIfExists('operators');
        
    }
    
}
