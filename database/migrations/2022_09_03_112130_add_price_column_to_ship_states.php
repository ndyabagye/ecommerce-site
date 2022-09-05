<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceColumnToShipStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ship_states', function (Blueprint $table) {
            $table->integer('shipping_amount')->after('state_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ship_states', function (Blueprint $table) {
            $table->dropColumn('shipping_amount');
        });
    }
}
