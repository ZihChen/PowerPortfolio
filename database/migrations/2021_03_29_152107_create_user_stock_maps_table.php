<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStockMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('UserStockMaps')) {

            Schema::create('UserStockMaps', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('stock_id');
                $table->integer('sort');
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
        Schema::dropIfExists('UserStockMaps');
    }
}
