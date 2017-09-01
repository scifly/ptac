<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEducatorsTableRemoveTeamIdsField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('educators', function (Blueprint $table) {
            $table->dropColumn('team_ids');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('educators', function (Blueprint $table) {
            $table->string('team_ids')->nullable()->comment('所属教职员工组IDs');
        });
    }
}
