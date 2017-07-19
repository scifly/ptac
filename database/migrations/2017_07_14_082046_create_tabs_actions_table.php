<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabsActionsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('tabs_actions')) {
            Schema::create('tabs_actions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('tab_id')->comment('卡片ID');
                $table->integer('action_id')->comment('控制器action ID');
                $table->boolean('default')->comment('是否为默认加载的控制器action');
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
        
        Schema::dropIfExists('tabs_actions');
        
    }
}
