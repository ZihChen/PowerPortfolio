<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStockPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('UserStockPositions')) {

            Schema::create('UserStockPositions', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('stock_id');
                $table->float('invested');
                $table->float('target_position');
                $table->float('avg_open');
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
        Schema::dropIfExists('UserStockPositions');
    }
}
