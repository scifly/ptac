<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableTabsAddIconIdField extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->integer('icon_id')->nullable()->comment('图标ID');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tabs', function (Blueprint $table) {
            $table->dropColumn('icon_id');
        });
    }
}
