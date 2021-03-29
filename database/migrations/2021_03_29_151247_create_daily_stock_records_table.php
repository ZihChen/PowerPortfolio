<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStockRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('DailyStockRecords')) {

            Schema::create('DailyStockRecords', function (Blueprint $table) {
                $table->id();
                $table->integer('stock_id');
                $table->date('date');
                $table->float('close_price');
                $table->float('high_price');
                $table->float('low_price');
                $table->float('change_percent');
                $table->float('rsv');
                $table->float('stochastic_k');
                $table->float('stochastic_d');
                $table->float('rsi');
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
        Schema::dropIfExists('DailyStockRecords');
    }
}
