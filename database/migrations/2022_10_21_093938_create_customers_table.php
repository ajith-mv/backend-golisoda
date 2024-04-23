<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string( 'last_name' )->nullable();
            $table->string( 'email');
            $table->string( 'mobile_no' )->nullable();
            $table->string( 'customer_no') ->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string( 'password' )->nullable();
            $table->timestamp('remember_token')->nullable();
            $table->date('dob')->nullable();
            $table->string( 'profile_image' )->nullable();
            $table->text('address');
            $table->enum( 'status', ['published', 'unpublished'])->default('published');
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
        Schema::dropIfExists('customers');
    }
}
