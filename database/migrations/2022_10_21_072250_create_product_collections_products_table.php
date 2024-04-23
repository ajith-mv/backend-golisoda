<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCollectionsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_collections_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_collection_id');
            $table->foreign('product_collection_id')->references('id')->on('product_collections')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->integer( 'order_by' )->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_collections_products');
    }
}
