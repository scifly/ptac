<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateMediaTypesTable
 */
class CreateMediaTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('media_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('媒体类型名称');
            $table->string('remark')->comment('媒体类型备注');
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
        Schema::drop('media_types');
    }
    
}
