<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponToCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->after('sub_total');
            $table->decimal('coupon_amount', 12,2)->nullable()->after('coupon_id');
            $table->unsignedBigInteger('shipping_fee_id')->nullable()->after('coupon_amount');
            $table->decimal('shipping_fee', 12,2)->nullable()->after('shipping_fee_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('coupon_id');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('shipping_fee_id');
            $table->dropColumn('shipping_fee');
        });
    }
}
