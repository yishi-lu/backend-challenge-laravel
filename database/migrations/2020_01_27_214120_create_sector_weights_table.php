<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectorWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sector_weights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etf_id')->nullable();
            $table->string('label', 100)->nullable();
            $table->string('data', 10)->nullable();
            $table->string('type', 10)->nullable();

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
        Schema::dropIfExists('sector_weights');
    }
}
