<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_no')->nullable();
            $table->decimal('amount', 8,2);
            $table->decimal('paid_amount', 8,2);
            $table->string('payment_type')->nullable();
            $table->string('payment_mode')->nullable();
            $table->longText('response')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'cancelled', 'paid', 'failed'])->default('pending');
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
        Schema::dropIfExists('payments');
    }
}
