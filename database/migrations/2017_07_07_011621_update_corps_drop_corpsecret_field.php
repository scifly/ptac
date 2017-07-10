<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorpsDropCorpsecretField extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('corps', function (Blueprint $table) {
            $table->dropColumn('corpsecret');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::table('corps', function (Blueprint $table) {
            $table->string('corpsecret', 64)->comment('管理组的凭证密钥');
        });
        
    }
    
}
