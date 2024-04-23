<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreLocatorPincodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_locator_pincodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_locator_id');
            $table->string('pincode');
            $table->string('order_by')->nullable();
            $table->string('added_by');
            $table->softDeletes();
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
        Schema::dropIfExists('store_locator_pincodes');
    }
}
