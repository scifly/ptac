<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMobilesTableAddIsdefaultField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('mobiles', function (Blueprint $table) {
            $table->boolean('isdefault')->comment('是否为默认的手机号码');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('mobiles', function (Blueprint $table) {
            $table->dropColumn('isdefault');
        });
    }
}
