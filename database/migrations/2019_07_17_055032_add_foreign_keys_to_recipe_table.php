<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('recipe', function(Blueprint $table)
		{
			$table->foreign('preparation_method_id', 'fk_recipe_preparation_method')->references('id')->on('preparation_method')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('serving_type_id', 'fk_recipe_serving_type')->references('id')->on('serving_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('recipe', function(Blueprint $table)
		{
			$table->dropForeign('fk_recipe_preparation_method');
			$table->dropForeign('fk_recipe_serving_type');
		});
	}

}
