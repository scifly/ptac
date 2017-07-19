<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
                $table->string('remark')->comment('备注');
                $table->string('controller')->comment('所属controller类名');
                $table->string('view')->nullable()->comment('对应的blade view名');
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
        Schema::dropIfExists('actions');
    }
}
