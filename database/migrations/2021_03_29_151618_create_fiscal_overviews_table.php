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
                $table->float('eps', 12, 6);
                $table->float('pe_ratio', 12, 6);
                $table->float('roa_ttm', 12, 6);
                $table->float('roe_ttm', 12, 6);
                $table->float('profit_margin', 12, 6);
                $table->float('operating_margin', 12, 6);
                $table->float('ev_to_revenue', 12, 6);
                $table->text('content', 65535);
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
