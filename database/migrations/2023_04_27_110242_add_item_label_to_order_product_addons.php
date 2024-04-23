<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemLabelToOrderProductAddons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_addons', function (Blueprint $table) {
            $table->string('addon_item_label')->nullable()->after('title');
            $table->unsignedBigInteger('addon_id')->after('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_product_addons', function (Blueprint $table) {
            $table->dropColumn('addon_item_label');
            $table->dropColumn('addon_id');
        });
    }
}
