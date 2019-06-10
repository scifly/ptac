<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id')->nullable()->comment('父菜单ID');
			$table->integer('menu_type_id');
			$table->string('name', 30)->comment('菜单名称');
			$table->string('uri')->nullable();
			$table->string('remark')->nullable()->comment('菜单备注');
			$table->integer('media_id')->nullable()->comment('图片ID');
			$table->integer('position')->nullable()->comment('菜单所处位置');
			$table->integer('icon_id')->nullable()->comment('图标ID');
			$table->timestamps();
			$table->boolean('enabled');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}
