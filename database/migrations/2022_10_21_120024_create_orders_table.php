<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string( 'order_no' );
            $table->unsignedBigInteger('shipping_options');
            $table->string( 'shipping_type' )->default('default');
            $table->decimal('amount', 8,2);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 8,2);
            $table->decimal('shipping_amount', 8,2);
            $table->decimal('discount_amount', 8,2);
            $table->decimal('coupon_amount', 8,2);
            $table->string('coupon_code')->nullable();
            $table->decimal('sub_total', 8,2);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('order_status_id');
            $table->enum('status',['pending', 'completed', 'cancelled']);
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
