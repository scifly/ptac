<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSyncedFieldToUsersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'synced')) {
                $table->boolean('synced')->comment('是否已同步到企业号');
            }
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'synced')) {
                $table->dropColumn('synced');
            }
        });
        
    }
    
}
