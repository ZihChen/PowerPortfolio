<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelativeStrengthIndicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('RelativeStrengthIndex')) {

            Schema::create('RelativeStrengthIndex', function (Blueprint $table) {
                $table->id();
                $table->integer('record_id');
                $table->date('date');
                $table->string('series_type');
                $table->string('interval');
                $table->integer('time_period');
                $table->float('avg_gain', 12, 6);
                $table->float('avg_loss', 12, 6);
                $table->float('rsi', 12, 6);
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
        Schema::dropIfExists('RelativeStrengthIndex');
    }
}
