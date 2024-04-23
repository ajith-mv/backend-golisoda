<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingDetailsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_name')->nullable()->after('billing_city');
            $table->string('shipping_email')->nullable()->after('shipping_name');
            $table->string('shipping_mobile_no')->nullable()->after('shipping_email');
            $table->string('shipping_address_line1')->nullable()->after('shipping_mobile_no');
            $table->string('shipping_address_line2')->nullable()->after('shipping_address_line1');
            $table->string('shipping_landmark')->nullable()->after('shipping_address_line2');
            $table->string('shipping_country')->nullable()->after('shipping_landmark');
            $table->string('shipping_post_code')->nullable()->after('shipping_country');
            $table->string('shipping_state')->nullable()->after('shipping_post_code');
            $table->string('shipping_city')->nullable()->after('shipping_state');
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
            $table->dropColumn('shipping_name');
            $table->dropColumn('shipping_email');
            $table->dropColumn('shipping_mobile_no');
            $table->dropColumn('shipping_address_line1');
            $table->dropColumn('shipping_address_line2');
            $table->dropColumn('shipping_landmark');
            $table->dropColumn('shipping_country');
            $table->dropColumn('shipping_post_code');
            $table->dropColumn('shipping_state');
            $table->dropColumn('shipping_city');
        });
    }
}
