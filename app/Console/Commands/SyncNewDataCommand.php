<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\AlphaAdvantageService;
use App\Services\DailyRecordService;
use App\Services\FiscalOverviewService;
use App\Services\RelativeStrengthIndexService;
use App\Services\StochasticOscillatorService;
use App\Services\StockService;
use Illuminate\Console\Command;

class SyncNewDataCommand extends Command
{
    const STANDARD_SYMBOL = 'QQQ';

    /** @var StockService $stockService */
    private $stockService;
    /** @var AlphaAdvantageService $alphaAdvantageService */
    private $alphaAdvantageService;
    /** @var FiscalOverviewService $fiscalOverviewService */
    private $fiscalOverviewService;
    /** @var DailyRecordService $dailyRecordService */
    private $dailyRecordService;
    /** @var StochasticOscillatorService $stochasticOscillatorService */
    private $stochasticOscillatorService;
    /** @var RelativeStrengthIndexService $relativeStrengthIndexService */
    private $relativeStrengthIndexService;

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
    protected $description = '[更新每日股價資訊]:php artisan sync:new_data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->stockService = app(StockService::class);
        $this->alphaAdvantageService = app(AlphaAdvantageService::class);
        $this->fiscalOverviewService = app(FiscalOverviewService::class);
        $this->dailyRecordService = app(DailyRecordService::class);
        $this->stochasticOscillatorService = app(StochasticOscillatorService::class);
        $this->relativeStrengthIndexService = app(RelativeStrengthIndexService::class);
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        //取得最後一次收盤的日期
        $res = $this->alphaAdvantageService->getStockLatestQuote(self::STANDARD_SYMBOL);

        $latest_trading_day = $res['date'];

        Stock::with(['latest_daily_record' => function ($query) use ($latest_trading_day) {

            $query->where('date', $latest_trading_day);
        }])
            ->each(function ($stock) {

                if (!empty($stock->latest_daily_record)) {

                    $this->info("\n" . $stock->symbol . ' ' . 'has been updated.');

                    return true;   //若此檔股票有最新收盤資訊則跳過
                }

                $stock_symbol = $stock->symbol;

                $this->info("\n" . 'Start to acquire latest quote:' . $stock->symbol);
                $stock_quote = $this->alphaAdvantageService->getStockLatestQuote($stock_symbol);
                sleep(10);

                //更新股價、指標
                if ($stock->quote_latest_refresh != $stock_quote['date']) {

                    $kd_indicator = $this->stochasticOscillatorService->calculateStochasticOscillator($stock, $stock_quote);

                    $rsi_indicator = $this->relativeStrengthIndexService->calculateRSI($stock, $stock_quote);

                    $new_daily_record = array_merge($stock_quote, $kd_indicator, $rsi_indicator);

                    $this->dailyRecordService->firstOrCreateDailyRecordByStock($stock, $new_daily_record);

                    $stock->quote_latest_refresh = $stock_quote['date'];
                }

                if ($stock->type == 'Equity') {

                    $this->info("\n" . 'Start to acquire latest fiscal overview...');
                    $fiscal_info = $this->alphaAdvantageService->getStockOverview($stock_symbol);
                    sleep(10);

                    if ($stock->fiscal_latest_refresh != $fiscal_info['latest_refresh']) {

                        $this->fiscalOverviewService->firstOrCreateFiscalOverview($stock, $fiscal_info);

                        $stock->fiscal_latest_refresh = $fiscal_info['latest_refresh'];
                    }

                }

                $stock->save();

                $this->info("\n" . $stock->symbol . ' ' . 'update completed.');
                sleep(15);
            });

        $this->info("\n" . 'All sync completed!');
    }
}
