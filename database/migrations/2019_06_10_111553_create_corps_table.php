<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corps', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 120)->comment('企业名称');
			$table->integer('company_id')->comment('所属运营者公司ID');
			$table->string('acronym', 20)->comment('企业名称缩写（首字母缩略词）');
			$table->string('corpid', 36)->comment('企业id');
			$table->string('contact_sync_secret', 50)->comment('"通讯录同步"应用Secret');
			$table->integer('department_id')->comment('对应的部门ID');
			$table->integer('menu_id')->comment('对应的菜单id');
			$table->string('access_token')->nullable()->comment('通讯录同步应用access_token');
			$table->string('encoding_aes_key')->comment('接收消息服务器配置项，用于加密消息体');
			$table->string('token')->comment('接收消息服务器配置项，用于生成签名');
			$table->integer('departmentid')->default(1)->comment('企业微信后台通讯录的根部门id');
			$table->char('mchid', 10)->nullable()->comment('微信支付商户号');
			$table->char('apikey', 32)->nullable()->comment('微信支付商户支付密钥');
			$table->timestamps();
			$table->dateTime('expire_at')->nullable()->comment('通讯录同步应用access_token到期时间');
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
		Schema::drop('corps');
	}

}
