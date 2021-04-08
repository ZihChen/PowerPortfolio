<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\AlphaAdvantageService;
use App\Services\DailyRecordService;
use App\Services\FiscalOverviewService;
use Illuminate\Console\Command;

class SyncNewDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:new_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var AlphaAdvantageService $alphaAdvantageService */
        $alphaAdvantageService = app(AlphaAdvantageService::class);

        /** @var DailyRecordService $dailyRecordService */
        $dailyRecordService = app(DailyRecordService::class);

        /** @var FiscalOverviewService $fiscalOverviewService */
        $fiscalOverviewService = app(FiscalOverviewService::class);

        //最後一次收盤的日期
        $res = $alphaAdvantageService->getStockLatestQuote('MSFT');

        $latest_trading_day = $res['date'];

        Stock::with(['latest_daily_record' => function ($query) use ($latest_trading_day) {

            $query->where('date', $latest_trading_day);
        }])
            ->each(function ($stock) use ($alphaAdvantageService, $dailyRecordService, $fiscalOverviewService) {

                if (!empty($stock->latest_daily_record)) {

                    $this->info("\n" . $stock->symbol . ' ' . 'has been updated.');

                    return true;   //若此檔股票有最新收盤資訊則跳過
                }

                $stock_symbol = $stock->symbol;

                $stock_quote = $alphaAdvantageService->getStockLatestQuote($stock_symbol);

                //更新股價、指標
                if ($stock->quote_latest_refresh != $stock_quote['date']) {

                    $kd_indicator = $dailyRecordService->calculateStochasticOscillator($stock, $stock_quote);

                    $rsi_indicator = $dailyRecordService->calculateRSI($stock, $stock_quote);

                    $new_daily_record = array_merge($stock_quote, $kd_indicator, $rsi_indicator);

                    $dailyRecordService->firstOrCreateDailyRecordByStock($stock, $new_daily_record);

                    $stock->quote_latest_refresh = $stock_quote['date'];
                }

                if ($stock->type == 'Equity') {

                    $fiscal_info = $alphaAdvantageService->getStockOverview($stock_symbol);

                    if ($stock->fiscal_latest_refresh != $fiscal_info['latest_refresh']) {

                        $fiscalOverviewService->firstOrCreateFiscalOverview($stock, $fiscal_info);

                        $stock->fiscal_latest_refresh = $fiscal_info['latest_refresh'];
                    }

                }

                $stock->save();

                $this->info("\n" . $stock->symbol . ' ' . 'update completed.');
            });

        $this->info("\n" . 'All data sync completed!');
    }
}
