<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStatusFieldEducatorAttendancesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('educator_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('educator_attendances', 'status')) {
                $table->tinyInteger('status')->comment('考勤状态');
            }
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('educator_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('educator_attendances', 'status')) {
                $table->dropColumn('status');
            }
        });
        
    }
}
