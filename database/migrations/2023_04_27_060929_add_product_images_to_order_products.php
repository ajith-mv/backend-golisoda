<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductImagesToOrderProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->longText('image')->nullable()->after('product_name');
            $table->decimal('strice_price', 12,2)->nullable()->after('price');
            $table->decimal('discount_price', 12,2)->nullable()->after('strice_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('strice_price');
            $table->dropColumn('discount_price');
        });
    }
}
