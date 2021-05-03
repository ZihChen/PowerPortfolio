<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovingAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('MovingAverage')) {

            Schema::create('MovingAverage', function (Blueprint $table) {
                $table->id();
                $table->integer('record_id')->index('ma_record_id');
                $table->integer('stock_id')->index('ma_stock_id');
                $table->date('date');
                $table->string('series_type');
                $table->string('interval');
                $table->integer('time_period');
                $table->float('ma', 12, 6);
                $table->timestamps();

                $table->index(['record_id', 'date'], 'ma_record_id_date_index');
                $table->index(['stock_id', 'date'], 'ma_stock_id_date_index');
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
        Schema::dropIfExists('MovingAverage');
    }
}
