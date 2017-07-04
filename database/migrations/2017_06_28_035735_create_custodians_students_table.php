<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustodiansStudentsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
    
        if (!Schema::hasTable('custodians_students')) {
            Schema::create('custodians_students', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('custodian_id')->comment('监护人ID');
                $table->integer('student_id')->comment('学生ID');
                $table->string('relationship', 60)->comment('关系');
                $table->timestamps();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
        Schema::dropIfExists('custodians_students');
        
    }
    
}
