<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etf_id')->nullable();
            $table->string('name')->nullable();
            $table->string('weight')->nullable();
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
        Schema::dropIfExists('country_weights');
    }
}
