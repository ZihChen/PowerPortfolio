<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiscalOverviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('FiscalOverview')) {

            Schema::create('FiscalOverview', function (Blueprint $table) {
                $table->id();
                $table->integer('stock_id');
                $table->float('eps');
                $table->float('pe_ratio');
                $table->float('roa_ttm');
                $table->float('roe_ttm');
                $table->float('profit_margin');
                $table->float('operating_margin');
                $table->float('ev_to_revenue');
                $table->date('latest_refresh');
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
        Schema::dropIfExists('FiscalOverview');
    }
}
