<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingMethodToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('shipping_method_type', ['standard', 'pickup_from_store'])->nullable()->after('shipping_city');
            $table->text('pickup_store_details')->nullable()->after('shipping_method_type');
            $table->decimal('coupon_percentage', 12,2)->nullable()->after('coupon_code');
            $table->text('coupon_details')->nullable()->after('coupon_percentage');
            $table->unsignedBigInteger('shipping_options')->nullable(true)->change();
            $table->string('shipping_type')->nullable(true)->change();
            $table->unsignedBigInteger('pickup_store_id')->after('shipping_method_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_method_type');
            $table->dropColumn('pickup_store_details');
            $table->dropColumn('coupon_percentage');
            $table->dropColumn('coupon_details');
            $table->dropColumn('pickup_store_id');
        });
    }
}
