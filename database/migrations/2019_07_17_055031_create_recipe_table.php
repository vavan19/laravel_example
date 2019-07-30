<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('recipe', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title', 1000);
			$table->integer('servings')->nullable();
			$table->string('image_url', 500)->nullable();
			$table->integer('preparation_method_id')->nullable()->index('fk_recipe_preparation_method');
			$table->string('preparation_time', 100)->nullable();
			$table->integer('serving_type_id')->nullable()->index('fk_recipe_serving_type');
			$table->integer('naminami_id')->nullable()->comment('nami-nami receipt id');
			$table->boolean('breakfast')->nullable()->default(0);
			$table->boolean('dinner')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('recipe');
	}

}
