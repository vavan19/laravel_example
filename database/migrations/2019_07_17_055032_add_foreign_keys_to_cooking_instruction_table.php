<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCookingInstructionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cooking_instruction', function(Blueprint $table)
		{
			$table->foreign('recipe_id', 'fk_cooking_instruction_recipe')->references('id')->on('recipe')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cooking_instruction', function(Blueprint $table)
		{
			$table->dropForeign('fk_cooking_instruction_recipe');
		});
	}

}
