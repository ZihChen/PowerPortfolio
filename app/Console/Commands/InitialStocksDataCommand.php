<?php

namespace App\Console\Commands;

use App\Imports\ExcelToCollectionField;
use App\Models\Stock;
use App\Services\AlphaAdvantageService;
use App\Services\DailyRecordService;
use App\Services\FiscalOverviewService;
use App\Services\RelativeStrengthIndexService;
use App\Services\StochasticOscillatorService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class InitialStocksDataCommand extends Command
{
    const INTERVAL = 'daily';
    const SERIAL_TYPE = 'close';
    const KD_PERIOD = 9;
    const RSI_PERIOD = 14;

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
     * @return int
     */
    public function handle()
    {
        $file_path = $this->option('file_path');

        $excel = Excel::toCollection(new ExcelToCollectionField(), $file_path);

        $symbols = $excel->first()->collapse();

        foreach ($symbols as $symbol) {

            $stock = Stock::where('symbol', $symbol)->first();

            if (!empty($stock)) continue;

            try {

                $search_result = $this->alphaAdvantageService->searchStockInfo($symbol);

                $best_match = $search_result[0];

                $insert_data = [
                    'symbol' => $best_match['symbol'],
                    'name' => $best_match['name'],
                    'type' => $best_match['type'],
                    'sector' => 'N/A',
                    'industry' => 'N/A',
                ];

                if ($best_match['type'] == 'Equity') {

                    $fiscal_overview = $this->alphaAdvantageService->getStockOverview($symbol);

                    $insert_data['sector'] = $fiscal_overview['sector'];
                    $insert_data['industry'] = $fiscal_overview['industry'];
                }

                DB::beginTransaction();

                $stock = $this->stockService->firstOrCreateStock($insert_data);

                if ($best_match['type'] == 'Equity') {

                    $this->fiscalOverviewService->firstOrCreateFiscalOverview($stock, $fiscal_overview);
                }

                $daily_records = $this->alphaAdvantageService->getDailyStockRecords($symbol);

                $kd_records = $this->alphaAdvantageService->getStockKDIndicatorRecords($symbol, self::INTERVAL, self::KD_PERIOD);

                $rsi_records = $this->alphaAdvantageService->getStockRsiIndicatorRecords($symbol, self::INTERVAL, self::SERIAL_TYPE, self::RSI_PERIOD);

                $this->dailyRecordService->insertDailyRecordsByStock($stock, $daily_records);

                $daily_records = $stock->daily_records;

                $this->stochasticOscillatorService->insertKdIndicatorByDailyRecords($daily_records, $kd_records, self::INTERVAL, self::KD_PERIOD);

                $this->relativeStrengthIndexService->insertRsiIndicatorByDailyRecords($daily_records, $rsi_records, self::INTERVAL, self::SERIAL_TYPE, self::RSI_PERIOD);

                $this->stockService->updateStockRefreshDate($stock, $stock->latest_daily_record->date, empty($fiscal_overview['latest_refresh']) ? Carbon::now()->toDateString() : $fiscal_overview['latest_refresh']);

                DB::commit();

                $this->info("\n" . $stock->symbol . ' ' . 'create completed.');

                sleep(60);

            } catch (\Throwable $e) {

                DB::rollBack();

                $this->warn('Error Message:' . $e->getMessage());

                $this->warn('line:' . $e->getTraceAsString());

                $this->warn('line:' . $e->getLine());

                if ($e->getCode() == 202) {

                    $this->warn($symbol . ' is not completed');

                    sleep(60);
                }
            }

        }

    }
}
