<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('peid_no');
            $table->string('tdlt_no');
            $table->string('header');
            $table->string('template_name');
            $table->string('sms_type')->nullable();
            $table->string('communication_type')->nullable();
            $table->longText('template_content');
            $table->enum('status', ['published', 'unpublished']);
            $table->unsignedBigInteger('added_by');
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
        Schema::dropIfExists('sms_templates');
    }
}
