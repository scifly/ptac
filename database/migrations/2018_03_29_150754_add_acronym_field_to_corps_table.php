<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAcronymFieldToCorpsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('corps', function (Blueprint $table) {
            if (!Schema::hasColumn('corps', 'acronym')) {
                $table->string('acronym', 20)->after('name')->comment('企业名称缩写（首字母缩略词）');
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
            if (Schema::hasColumn('corps', 'acronym')) {
                $table->dropColumn('acronym');
            }
        });
        
    }
    
}
