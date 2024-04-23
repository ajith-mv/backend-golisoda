<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductWarrantiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_warranties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_product_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warranty_id');
            $table->string('warranty_name');
            $table->string('description')->nullable();
            $table->integer('warranty_period');
            $table->enum('warranty_period_type', ['day', 'month', 'year']);
            $table->date('warranty_start_date');
            $table->date('warranty_end_date');
            $table->enum('status', ['active', 'inactive']);
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
        Schema::dropIfExists('order_product_warranties');
    }
}
