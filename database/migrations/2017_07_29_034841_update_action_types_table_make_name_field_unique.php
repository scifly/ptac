<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionTypesTableMakeNameFieldUnique extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('action_types', function (Blueprint $table) {
            $table->unique('name');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('action_types', function (Blueprint $table) {
            $table->unique('name', null);
        });
    }
}
