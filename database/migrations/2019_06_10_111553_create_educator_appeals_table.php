<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateEducatorAppealsTable
 */
class CreateEducatorAppealsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('educator_appeals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('educator_id')->comment('教职员工ID');
            $table->string('ea_ids')->comment('考勤记录IDs');
            $table->string('appeal_content')->comment('申诉内容(考勤/会议/其他)');
            $table->integer('procedure_log_id')->comment('相关流程日志ID');
            $table->string('approver_educator_ids')->comment('审批人教职员工IDs');
            $table->string('related_educator_ids')->comment('相关人教职员工IDs');
            $table->timestamps();
            $table->boolean('status')->comment('审批状态 0 - 通过 1 - 拒绝 2 - 待审');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('educator_appeals');
    }
    
}
