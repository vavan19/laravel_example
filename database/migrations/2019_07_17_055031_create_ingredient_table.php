<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIngredientTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ingredient', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 100)->nullable()->unique('idx_ingredient');
			$table->integer('real_ingredient_id')->nullable()->comment('for future');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ingredient');
	}

}
