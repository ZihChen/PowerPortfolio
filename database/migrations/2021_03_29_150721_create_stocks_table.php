<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('Stocks')) {

            Schema::create('Stocks', function (Blueprint $table) {
                $table->id();
                $table->string('symbol');
                $table->string('name');
                $table->string('type');
                $table->string('sector');
                $table->string('industry');
                $table->dateTime('quote_latest_refresh');
                $table->dateTime('fiscal_latest_refresh');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Stocks');
    }
}
