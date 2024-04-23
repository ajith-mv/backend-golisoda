<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string( 'coupon_name' );
            $table->string( 'coupon_code' )->nullable();
            $table->string( 'coupon_sku' );
            $table->date( 'start_date' );
            $table->date( 'end_date' )->nullable();
            $table->integer( 'quantity' );
            $table->integer( 'used_quantity' )->nullable();
            $table->enum( 'calculate_type', ['percentage', 'fixed_amount'] );
            $table->decimal( 'calculate_value' );
            $table->decimal( 'minimum_order_value' )->nullable();
            $table->enum( 'is_discount_on', ['yes', 'no'] )->comment('coupon or discount we can idendify');
            $table->integer( 'coupon_type' )->comment('product,category,customer,all_orders from maincategories table');
            $table->integer( 'repeated_use_count' )->default(0);
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
        Schema::dropIfExists('coupons');
    }
}
