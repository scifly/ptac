<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateConsumptionsTable
 */
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
            $table->string('location')->nullable()->comment('消费地点');
            $table->string('machineid')->nullable()->comment('消费机id');
            $table->boolean('ctype')->comment('消费类型，0：充值，1：消费');
            $table->decimal('amount', 6)->comment('消费金额');
            $table->dateTime('ctime')->comment('消费时间');
            $table->string('merchant')->comment('消费内容');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('consumptions');
    }
    
}
