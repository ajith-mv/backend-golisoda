<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryOtpToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_otp')->after('order_status_id')->nullable();
            $table->string('otp_verified_at')->after('delivery_otp')->nullable();
            $table->string('otp_verified_by')->after('otp_verified_at')->nullable();
            $table->string('delivery_document')->after('otp_verified_by')->nullable();
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
            $table->dropColumn('delivery_otp');
            $table->dropColumn('otp_verified_at');
            $table->dropColumn('otp_verified_by');
            $table->dropColumn('delivery_document');
        });
    }
}
