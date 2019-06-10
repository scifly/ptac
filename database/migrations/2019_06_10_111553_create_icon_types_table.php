<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateIconTypesTable
 */
class CreateIconTypesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('icon_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60)->comment('图标类型名称');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::drop('icon_types');
    }
    
}
