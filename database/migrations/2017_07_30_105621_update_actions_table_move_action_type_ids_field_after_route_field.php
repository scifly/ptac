<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionsTableMoveActionTypeIdsFieldAfterRouteField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('action_type_ids', '60')->nullable()->after('route')
                ->comment('HTTP请求类型IDs')->change();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('action_type_ids')->nullable()->comment('HTTP请求类型IDs')->change();
        });
    }
}
