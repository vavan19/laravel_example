<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ean', 25)->unique();
            $table->text('title');
            $table->float('weight');
            $table->float('price');
            $table->string('shelf_life');
            $table->string('storage_temp');
            $table->timestamps();
            $table->integer('producer_id'); //->unsigned();
            $table->integer('product_group_id'); //->unsigned();
            $table->integer('sheet_id'); //->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
