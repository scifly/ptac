<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ordersn', 20)->comment('订单序列号');
			$table->integer('user_id')->comment('微信支付用户ID');
			$table->integer('pay_user_id')->comment('实际付款用户ID');
			$table->boolean('status')->comment('订单状态');
			$table->integer('combo_type_id')->comment('套餐类型ID');
			$table->boolean('payment')->comment('支付类型（直付、代缴）');
			$table->string('transactionid')->comment('微信订单号');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
