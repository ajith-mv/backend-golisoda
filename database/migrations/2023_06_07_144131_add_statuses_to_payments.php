<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusesToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('enc_request')->nullable()->after('response');
            $table->text('enc_response')->nullable()->after('enc_request');
            $table->text('enc_response_decrypted')->nullable()->after('enc_response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('enc_request');
            $table->dropColumn('enc_response');
            $table->dropColumn('enc_response_decrypted');
        });
    }
}
