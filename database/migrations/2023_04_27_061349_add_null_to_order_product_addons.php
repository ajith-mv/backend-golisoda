<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullToOrderProductAddons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_addons', function (Blueprint $table) {
            $table->decimal('amount', 12,2)->nullable(true)->change();
            $table->text('description')->nullable()->after('amount');
            $table->longText('icon')->after('description')->nullable();
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
            $table->dropColumn('description');
            $table->dropColumn('icon');
        });
    }
}
