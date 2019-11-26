<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->unsigned()->nullable();
            $table->foreign('cart_id')->references('id')
                  ->on('cart_users')->onUpdate('cascade')->onDelete('cascade');

            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')
                ->on('products')->onUpdate('cascade')->onDelete('cascade');

            $table->string('name');
            $table->integer('price');
            $table->integer('quantity')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_products');
    }
}
