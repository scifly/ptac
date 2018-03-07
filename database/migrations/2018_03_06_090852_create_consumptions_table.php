<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumptionsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('consumptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->comment('学生id');
            $table->string('location', 255)->nullable()->comment('消费地点');
            $table->string('machineid', 255)->nullable()->comment('消费机id');
            $table->smallInteger('ctype')->comment('消费类型');
            $table->decimal('amount', 6, 2)->comment('消费金额');
            $table->dateTime('ctime')->comment('消费时间');
            $table->string('merchant', 255)->comment('消费内容');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('consumptions');
    }
}
