<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRowTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('row', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('recipe_id')->index('fk_row_recipe');
			$table->boolean('is_header')->nullable()->comment('false if contains an ingredient
true if contains header');
			$table->string('amount', 30)->nullable();
			$table->integer('unit_id')->nullable()->index('fk_row_unit');
			$table->decimal('grams', 8, 4)->nullable();
			$table->integer('ingredient_id')->nullable()->index('fk_row_ingredient');
			$table->integer('in_order')->nullable()->comment('if line have multiple options, rows have same in_order value');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('row');
	}

}
