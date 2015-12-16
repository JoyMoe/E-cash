<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class Order extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchandiser_id')->unsigned();
            $table->string('trade_no')->unique();
            $table->string('subject');
            $table->float('amount');
            $table->text('description')->nullable();
            $table->string('returnUrl');
            $table->string('notifyUrl');
            $table->enum('gateway', array('alipay', 'paypal', 'unionpay', 'wechat'))->nullable();
            $table->string('transaction_id')->nullable();
            $table->float('received')->default(0);
            $table->enum('status', array('pending', 'processing', 'done', 'refunded', 'cancelled'))->default('pending');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('merchandiser_id')->references('id')->on('merchandisers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
