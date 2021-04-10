<?php

namespace App\Console\Commands;

use App\Imports\ExcelToCollectionField;
use App\Models\Stock;
use App\Services\AlphaAdvantageService;
use App\Services\DailyRecordService;
use App\Services\FiscalOverviewService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class InitialStocksDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:data {--file_path= : csv or excel file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command line:php artisan init:data --file_path=app/Form/stock_symbols_01.xlsx';

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

        $file_path = $this->option('file_path');

        $excel = Excel::toCollection(new ExcelToCollectionField(), $file_path);

        $symbols = $excel->first()->collapse();

        foreach ($symbols as $symbol) {

            $stock = Stock::where('symbol', $symbol)->first();

            if (!empty($stock)) continue;

            try {

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

            } catch (\Throwable $e) {

                $this->warn('Error Message:' . $e->getMessage());

                if ($e->getCode() == 202) {

                    $this->warn($symbol . ' is not completed');

                    sleep(60);
                }
            }

        }

    }
}
