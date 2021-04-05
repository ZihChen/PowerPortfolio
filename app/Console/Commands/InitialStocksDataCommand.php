<?php

namespace App\Console\Commands;

use App\Services\AlphaAdvantageService;
use App\Services\DailyRecordService;
use App\Services\FiscalOverviewService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InitialStocksDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:data';

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
        /** @var StockService $stockService */
        $stockService = app(StockService::class);

        /** @var AlphaAdvantageService $alphaAdvantageService */
        $alphaAdvantageService = app(AlphaAdvantageService::class);

        /** @var FiscalOverviewService $fiscalOverviewService */
        $fiscalOverviewService = app(FiscalOverviewService::class);

        /** @var DailyRecordService $dailyRecordService */
        $dailyRecordService = app(DailyRecordService::class);

        foreach ($this->symbols() as $symbol) {

            $search_result = $alphaAdvantageService->searchStockInfo($symbol);

            $best_match = $search_result[0];

            $insert_data = [
                'symbol' => $best_match['symbol'],
                'name' => $best_match['name'],
                'type' => $best_match['type'],
                'sector' => 'N/A',
                'industry' => 'N/A',
            ];

            if ($best_match['type'] == 'Equity') {

                $fiscal_overview = $alphaAdvantageService->getStockOverview($symbol);

                $insert_data['sector'] = $fiscal_overview['sector'];
                $insert_data['industry'] = $fiscal_overview['industry'];
            }

            $stock = $stockService->firstOrCreateStock($insert_data);

            if ($best_match['type'] == 'Equity') {

                $fiscalOverviewService->firstOrCreateFiscalOverview($stock, $fiscal_overview);
            }

            $daily_records = $alphaAdvantageService->getDailyStockRecords($symbol);

            $kd_records = $alphaAdvantageService->getStockKDIndicatorRecords($symbol);

            $rsi_records = $alphaAdvantageService->getStockRsiIndicator($symbol);

            $dailyRecordService->insertDailyRecordsByStock($stock, $daily_records, $kd_records, $rsi_records);

            $dailyRecordService->calculateRsvAndUpdateLatestThreeRecords($stock);

            $stockService->updateStockRefreshDate($stock, $stock->latest_daily_record->date, empty($fiscal_overview['latest_refresh']) ? Carbon::now()->toDateString() : $fiscal_overview['latest_refresh']);

            $this->info("\n" . $stock->symbol . ' ' . 'create completed.');

            sleep(60);
        }

    }

    private function symbols()
    {
        return [
//            'VTI',
//            'QQQ',
//            'CQQQ',
//            'SOXX',
//            'ARKK',
//            'XBI',
//            'SKYY',
//            'VXX',
            'MSFT',
            'AAPL',
            'MA',
            'V',
            'TSLA',
            'AMZN',
            'GOOG',
            'FB',
            'ADBE',
            'CRM',
            'TSM',
            'NVDA',
            'AMD',
            'SQ',
            'PYPL',
            'BABA',
            'JNJ',
            'DIS',
            'JPM',
            'KO',
            'MCD',
            'UNH',
            'SNOW',
            'PLTR',
            'NET',
            'FSLY',
            'ABNB',
            'UBER',
        ];

    }

}
