<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DelAvatarMediaidFieldFromUsersTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar_mediaid')) {
                $table->dropColumn('avatar_mediaid');
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
            if (!Schema::hasColumn('users', 'avatar_mediaid')) {
                $table->string('avatar_mediaid')->comment('微信号');
            }
        });
    }
    
}
