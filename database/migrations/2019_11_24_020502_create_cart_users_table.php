<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->unique();
            // $table->primary(['id', 'user_id']);
            // $table->foreign('user_id')->references('id')->on('users')
            //       ->onUpdate('cascade')->onDelete('set null');
            $table->string('billing_email')->default('init');
            $table->string('billing_name')->default('init')->nullable();
            $table->string('billing_address')->default('init');
            $table->string('billing_city')->default('init');
            $table->string('billing_province')->default('init');
            $table->string('billing_phone')->default('init');
            $table->string('billing_name_on_card')->default('init')->nullable();
            $table->integer('billing_discount')->default(0);
            $table->string('billing_discount_code')->nullable();
            $table->integer('billing_subtotal')->default(0);
            $table->integer('billing_tax')->default(0);
            $table->integer('billing_total')->default(0);
            $table->string('payment_gateway')->default('card');
            $table->boolean('shipped')->default(false);
            $table->string('error')->nullable();
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
        Schema::dropIfExists('cart_users');
    }
}
