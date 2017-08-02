<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMenusTableAddLftRghtMediaIdSchoolIdFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('school_id')->after('remark')->comment('所属学校ID');
            $table->integer('lft')->nullable()->after('school_id');
            $table->integer('rght')->nullable()->after('lft');
            $table->integer('media_id')->nullable()->after('rght')->comment('图片ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('school_id');
            $table->dropColumn('lft');
            $table->dropColumn('rght');
            $table->dropColumn('media_id');
        });
    }
}
