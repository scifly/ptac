<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateSchoolTypesTable
 */
class CreateSchoolTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('school_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('学校类型名称');
            $table->string('remark')->comment('学校类型备注');
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
        Schema::drop('school_types');
    }
    
}
