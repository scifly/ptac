<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCardTurnstileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('card_turnstile', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('card_id');
			$table->integer('turnstile_id');
			$table->date('start_date')->nullable()->comment('起始日期');
			$table->date('end_date')->nullable()->comment('截止日期');
			$table->string('ruleids', 20)->comment('通行规则ids');
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
		Schema::drop('card_turnstile');
	}

}
