<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAvatarUrlFieldFromUsersTableToNullable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->comment('头像URL')->nullable()->change();
        });
        
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->comment('头像URL');
        });
        
    }
    
}
