<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStochasticOscillatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('StochasticOscillator')) {

            Schema::create('StochasticOscillator', function (Blueprint $table) {
                $table->id();
                $table->integer('record_id')->index('kd_record_id');
                $table->integer('stock_id')->index('kd_stock_id');
                $table->date('date');
                $table->string('interval');
                $table->integer('fastk_period');
                $table->integer('slowk_period');
                $table->integer('slowd_period');
                $table->float('rsv', 12, 6);
                $table->float('stochastic_k', 12, 6);
                $table->float('stochastic_d', 12, 6);
                $table->timestamps();

                $table->index(['record_id', 'date'], 'kd_record_id_date_index');
                $table->index(['stock_id', 'date'], 'kd_stock_id_date_index');
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
        Schema::dropIfExists('StochasticOscillator');
    }
}
