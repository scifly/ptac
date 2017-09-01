<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEducatorsTeams extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        if (!Schema::hasTable('educators_teams')) {
            Schema::create('educators_teams', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('educator_id')->comment('教职员工ID');
                $table->integer('team_id')->comment('教职员工组ID');
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
        Schema::dropIfExists('educators_teams');
    }
}
