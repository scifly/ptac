<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('tabs')) {
            Schema::create('tabs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->comment('卡片名称');
                $table->string('remark')->nullable()->comment('卡片备注');
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
        Schema::dropIfExists('tabs');
    }
}
