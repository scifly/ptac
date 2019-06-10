<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 6);
			$table->string('remark');
			$table->integer('tab_id')->nullable();
			$table->integer('school_id')->comment('应用模块所属的学校id');
			$table->integer('media_id')->comment('模块图标媒体id');
			$table->integer('group_id')->nullable()->default(0)->comment('应用模块所属角色id');
			$table->string('uri')->nullable();
			$table->integer('order')->nullable()->comment('模块位置');
			$table->boolean('isfree')->comment('是否为免费模块');
			$table->timestamps();
			$table->boolean('enabled');
			$table->index(['tab_id','school_id','media_id','group_id'], 'tab_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules');
	}

}
