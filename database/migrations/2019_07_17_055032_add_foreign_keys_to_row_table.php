<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRowTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('row', function(Blueprint $table)
		{
			$table->foreign('ingredient_id', 'fk_row_ingredient')->references('id')->on('ingredient')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('recipe_id', 'fk_row_recipe')->references('id')->on('recipe')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('unit_id', 'fk_row_unit')->references('id')->on('unit')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('row', function(Blueprint $table)
		{
			$table->dropForeign('fk_row_ingredient');
			$table->dropForeign('fk_row_recipe');
			$table->dropForeign('fk_row_unit');
		});
	}

}
