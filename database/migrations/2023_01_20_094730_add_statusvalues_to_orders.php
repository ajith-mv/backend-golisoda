<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusvaluesToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE gbs_orders MODIFY `status` ENUM('pending', 'placed', 'shipped', 'delivered', 'cancelled', 'payment_pending') NOT NULL");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE gbs_orders MODIFY `status` ENUM('pending', 'completed', 'cancelled') NOT NULL");
    }
}
