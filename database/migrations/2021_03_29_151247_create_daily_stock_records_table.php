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
                $table->date('date')->index('daily_record_date');
                $table->float('close_price', 12, 6);
                $table->float('high_price', 12, 6);
                $table->float('low_price', 12, 6);
                $table->float('change_percent', 12, 6);
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
