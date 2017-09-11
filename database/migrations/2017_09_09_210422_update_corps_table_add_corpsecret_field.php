<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCorpsTableAddCorpsecretField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('corps', function (Blueprint $table) {
            $table->char('corpsecret', 64)->comment('Secret');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('corps', function (Blueprint $table) {
            $table->dropColumn('corpsecret');
        });
    }
}
