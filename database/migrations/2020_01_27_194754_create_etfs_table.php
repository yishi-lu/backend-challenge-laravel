<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etfs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fundName')->nullable();
            $table->string('fundTicker', 10)->nullable();
            $table->string('fundUri');
            $table->string('ter', 20)->nullable();
            $table->string('nav', 20)->nullable();
            $table->string('aum', 20)->nullable();
            $table->date('asOfDate')->nullable();
            $table->string('fundFilter', 10)->nullable();
            $table->string('domicile')->nullable();
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('etfs');
        Schema::enableForeignKeyConstraints();
    }
}
