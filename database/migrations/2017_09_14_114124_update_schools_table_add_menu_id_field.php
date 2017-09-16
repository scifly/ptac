<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchoolsTableAddMenuIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('schools', function (Blueprint $table) {
            $table->integer('menu_id')->comment('对应的菜单ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('menu_id');
        });
    }
}
