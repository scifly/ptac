<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('actions')) {
            Schema::create('actions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment('method/function名称');
                $table->string('action_type_ids', 60)->nullable()->comment('HTTP请求类型IDs');
                $table->string('remark')->nullable()->comment('备注');
                $table->string('controller')->comment('所属controller类名');
                $table->string('view')->nullable()->comment('对应的blade view名');
                $table->string('method');
                $table->string('js')->nullable();
                $table->string('route')->nullable();
                $table->timestamps();
                $table->boolean('enabled');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('actions');
    }
    
}
