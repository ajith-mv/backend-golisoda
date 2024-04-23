<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->unsignedBigInteger('address_type_id');
            $table->string( 'name' )->nullable();
            $table->string( 'email' )->nullable();
            $table->string( 'mobile_no' )->nullable();
            $table->string( 'address_line1' )->nullable();
            $table->string( 'address_line2' )->nullable();
            $table->string( 'landmark' )->nullable();
            $table->string( 'city_id' )->nullable();
            $table->string( 'state_id' )->nullable();
            $table->string( 'country_id' )->nullable();
            $table->string( 'post_code_id' )->nullable();
            $table->integer('is_default')->default(0);
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
        Schema::dropIfExists('customer_addresses');
    }
}
