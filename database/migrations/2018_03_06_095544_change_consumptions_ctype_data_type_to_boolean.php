<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConsumptionsCtypeDataTypeToBoolean extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('consumptions', function (Blueprint $table) {
            $table->boolean('ctype')->comment('消费类型，0：充值，1：消费')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('consumptions', function (Blueprint $table) {
            $table->smallInteger('ctype')->comment('消费类型')->change();
        });
    }
}
