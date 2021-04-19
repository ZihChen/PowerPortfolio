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
                $table->float('units', 12, 6);
                $table->float('avg_open', 12, 6);
                $table->float('target_position', 12, 6);
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
