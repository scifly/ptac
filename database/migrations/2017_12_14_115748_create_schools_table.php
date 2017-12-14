<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSchoolsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('school_type_id')->comment('学校类型ID');
                $table->integer('menu_id');
                $table->string('name')->comment('学校名称');
                $table->string('address')->comment('学校地址');
                $table->float('longitude', 10, 0)->nullable()->comment('学校所处经度');
                $table->float('latitude', 10, 0)->nullable()->comment('学校所处纬度');
                $table->integer('corp_id')->comment('学校所属企业ID');
                $table->integer('sms_max_cnt')->nullable()->comment('学校短信配额');
                $table->integer('sms_used')->nullable()->comment('短信已使用量');
                $table->timestamps();
                $table->boolean('enabled');
                $table->integer('department_id')->comment('对应的部门ID');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('schools');
    }
    
}
