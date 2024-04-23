<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingInOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->longText('tracking_link')->after('status_name')->nullable();
            $table->string('tracking_number')->after('tracking_link')->nullable();
            $table->longText('shipping_medium')->after('tracking_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('tracking_link');
            $table->dropColumn('tracking_number');
            $table->dropColumn('shipping_medium');
        });
    }
}
