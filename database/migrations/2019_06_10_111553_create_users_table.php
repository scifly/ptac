<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('group_id')->comment('所属角色/权限ID');
			$table->integer('card_id')->nullable();
			$table->string('username')->comment('用户名');
			$table->string('remember_token')->nullable()->comment('"记住我"令牌，登录时用');
			$table->string('password', 60)->comment('密码');
			$table->string('email')->nullable()->comment('电子邮箱');
			$table->boolean('gender')->comment('性别');
			$table->string('realname', 60)->comment('真实姓名');
			$table->string('avatar_url')->nullable()->default('')->comment('头像URL');
			$table->string('userid', 64)->comment('成员userid');
			$table->string('english_name', 64)->nullable()->comment('英文名');
			$table->boolean('isleader')->nullable()->default(0)->comment('上级字段，标识是否为上级。第三方暂不支持');
			$table->string('position', 64)->nullable()->comment('职位信息');
			$table->string('telephone', 64)->nullable()->comment('座机号码');
			$table->integer('order')->nullable()->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面');
			$table->timestamps();
			$table->boolean('synced')->default(0)->comment('是否已同步到企业号');
			$table->boolean('subscribed')->default(0)->comment('是否关注企业微信');
			$table->boolean('enabled')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
