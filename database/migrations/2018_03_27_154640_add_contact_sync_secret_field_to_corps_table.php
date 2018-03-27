<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddContactSyncSecretFieldToCorpsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        Schema::table('corps', function (Blueprint $table) {
            if (!Schema::hasColumn('corps', 'contact_sync_secret')) {
                $table->string('contact_sync_secret', 50)->comment('"通讯录同步"应用Secret');
            }
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    
        Schema::table('corps', function (Blueprint $table) {
            if (Schema::hasColumn('corps', 'contact_sync_secret')) {
                $table->dropColumn('contact_sync_secret');
            }
        });
        
    }
}
