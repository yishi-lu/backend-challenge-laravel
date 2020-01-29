<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopHoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_holdings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etf_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('market_value', 100)->nullable();
            $table->string('par_value', 100)->nullable();
            $table->string('total_mkt_cap_m', 100)->nullable();
            $table->string('ISIN', 100)->nullable();
            $table->string('shares', 100)->nullable();
            $table->string('ticker', 100)->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->timestamps();

            $table->foreign('etf_id')->references('id')->on('etfs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_holdings');
    }
}
