<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreatePassageRulesTable
 */
class CreatePassageRulesTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('passage_rules', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('school_id')->comment('学校id');
            $table->string('name', 60)->comment('通行规则名称');
            $table->integer('ruleid')->comment('通行规则id: 2 - 254');
            $table->text('targets')->nullable()->comment('作用范围');
            $table->date('start_date')->comment('通行规则起始日期');
            $table->date('end_date')->comment('通行规则结束日期');
            $table->char('statuses', 7)->comment('适用日：Mon - Sun');
            $table->string('tr1', 16)->default('00:00 - 00:00')->comment('时段1：00:00 - 13:33');
            $table->string('tr2', 16)->default('00:00 - 00:00')->comment('时段2');
            $table->string('tr3', 16)->default('00:00 - 00:00')->comment('时段3');
            $table->integer('related_ruleid')->default(0)->comment('关联的通行规则id');
            $table->timestamps();
            $table->boolean('enabled')->default(1)->comment('通行规则状态');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('passage_rules');
    }
    
}
