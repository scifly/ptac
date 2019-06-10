<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRuleTurnstileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rule_turnstile', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->integer('turnstile_id')->comment('门禁id');
			$table->boolean('door')->comment('门编号：1 - 4');
			$table->integer('passage_rule_id')->comment('通行规则id');
			$table->timestamps();
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
		Schema::drop('rule_turnstile');
	}

}
