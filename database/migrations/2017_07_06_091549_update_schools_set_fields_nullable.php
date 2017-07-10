<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSchoolsSetFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        
        Schema::table('schools', function(Blueprint $table) {
            $table->float('longitude')->nullable()->comment('学校所处经度')->change();
            $table->float('latitude')->nullable()->comment('学校所处纬度')->change();
            $table->integer('sms_max_cnt')->nullable()->comment('学校短信配额')->change();
            $table->integer('sms_used')->nullable()->comment('短信已使用量')->change();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    
        Schema::table('schools', function(Blueprint $table) {
            $table->float('longitude')->nullable(false)->comment('学校所处经度')->change();
            $table->float('latitude')->nullable(false)->comment('学校所处纬度')->change();
            $table->integer('sms_max_cnt')->nullable(false)->comment('学校短信配额')->change();
            $table->integer('sms_used')->nullable(false)->comment('短信已使用量')->change();
        });
    
    }
}
