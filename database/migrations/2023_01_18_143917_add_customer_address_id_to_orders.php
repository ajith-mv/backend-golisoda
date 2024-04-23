<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerAddressIdToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('billing_name')->after('sub_total')->nullable();
            $table->string('billing_email')->after('billing_name')->nullable();
            $table->string('billing_mobile_no')->after('billing_email')->nullable();
            $table->string('billing_address_line1')->after('billing_mobile_no')->nullable();
            $table->string('billing_address_line2')->after('billing_address_line1')->nullable();
            $table->string('billing_landmark')->after('billing_address_line2')->nullable();
            $table->string('billing_country')->after('billing_landmark')->nullable();
            $table->string('billing_post_code')->after('billing_country')->nullable();
            $table->string('billing_state')->after('billing_post_code')->nullable();
            $table->string('billing_city')->after('billing_state')->nullable();
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
            $table->dropColumn('billing_name');
            $table->dropColumn('billing_email');
            $table->dropColumn('billing_mobile_no');
            $table->dropColumn('billing_address_line1');
            $table->dropColumn('billing_address_line2');
            $table->dropColumn('billing_landmark');
            $table->dropColumn('billing_country');
            $table->dropColumn('billing_post_code');
            $table->dropColumn('billing_state');
            $table->dropColumn('billing_city');
        });
    }
}
