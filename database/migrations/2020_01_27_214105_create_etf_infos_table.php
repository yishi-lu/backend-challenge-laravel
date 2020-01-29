<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtfInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etf_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('etf_id')->nullable();
            $table->string('key_feature', 3000)->nullable();
            $table->string('about', 3000)->nullable();
            $table->string('primary_benchmark', 100)->nullable();
            $table->string('secondary_benchmark', 100)->nullable();
            $table->string('inception', 20)->nullable();
            $table->string('options', 5)->nullable();
            $table->string('gross_expense_ratio', 10)->nullable();
            $table->string('base_currency', 5)->nullable();
            $table->string('investment_manager', 100)->nullable();
            $table->string('management_team', 100)->nullable();
            $table->string('sub_advisor', 100)->nullable();
            $table->string('distributor', 100)->nullable();
            $table->string('distribution_frequency', 100)->nullable();
            $table->string('trustee', 100)->nullable();
            $table->string('marketing_agent', 100)->nullable();
            $table->string('gold_custodian', 100)->nullable();
            $table->string('sponsor', 100)->nullable();
            $table->string('exchange', 100)->nullable();
            $table->string('listing_date', 20)->nullable();
            $table->string('trading_currency', 5)->nullable();
            $table->string('CUSIP', 100)->nullable();
            $table->string('ISIN', 20)->nullable();

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
        Schema::dropIfExists('etf_infos');
    }
}
