<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string( 'combo_name' );
            $table->string('slug')->nullable();
            $table->string( 'tag_line' )->nullable();
            $table->enum('show_home_page', ['yes', 'no'])->default('no')->nullable();
            $table->integer( 'order_by' )->nullable();
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
        Schema::dropIfExists('combos');
    }
}
