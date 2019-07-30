<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTagRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tag_recipe', function(Blueprint $table)
		{
			$table->foreign('recipe_id', 'fk_tag_recipe_recipe')->references('id')->on('recipe')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('tag_id', 'fk_tag_recipe_tag')->references('id')->on('tag')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tag_recipe', function(Blueprint $table)
		{
			$table->dropForeign('fk_tag_recipe_recipe');
			$table->dropForeign('fk_tag_recipe_tag');
		});
	}

}
