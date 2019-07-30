<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tag_recipe', function(Blueprint $table)
		{
			$table->integer('tag_id');
			$table->integer('recipe_id')->index('fk_tag_recipe_recipe');
			$table->primary(['tag_id','recipe_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tag_recipe');
	}

}
