<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-31
 * Time: 18:40
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockService;
use App\Models\UserStockPositionService;
use App\Services\YahooFinanceService;
use App\Services\UserStockService;

class DashboardController
{
    protected $stockService;

    protected $userStockService;

    protected $userStockPositionService;

    protected $yahooFinanceService;

    /**
     * DashboardController constructor.
     * @param StockService $stockService
     * @param UserStockService $userStockService
     * @param UserStockPositionService $userStockPositionService
     * @param YahooFinanceService $yahooFinanceService
     */
    public function __construct(StockService $stockService,
                                UserStockService $userStockService,
                                UserStockPositionService $userStockPositionService,
                                YahooFinanceService $yahooFinanceService)
    {
        $this->stockService = $stockService;
        $this->userStockService = $userStockService;
        $this->userStockPositionService = $userStockPositionService;
        $this->yahooFinanceService = $yahooFinanceService;
    }

    public function getDashboard(Request $request)
    {
        $user = $request->user();

        $current_page = $request->get('page', 1);

        $limit = $request->get('limit', 12);

        $select_option_column = $request->get('column', 'symbol');

        $select_option_order = $request->get('order', 'asc');

        $interval = $request->get('interval', 'daily');

        $kd_period = $request->get('kd_period', 9);

        $rsi_period = $request->get('rsi_period', 14);

        $latest_trade_date = $this->yahooFinanceService->getLatestTradeDate();

        $stock_count = $this->userStockService->getUserStockCount($user);

        $total_pages = ceil($stock_count / $limit);    //總頁數

        $stocks = $this->stockService->getStocksByPaginate($user, $current_page, $limit, [
            'latest_daily_record'
        ]);

        $stock_positions = $this->userStockPositionService->getStockPositionsByUser($user);

        $stocks = $stocks->map(function ($stock) use ($stock_positions, $interval, $kd_period, $rsi_period, $latest_trade_date) {

            $latest_daily_record = optional($stock->latest_daily_record);

            $kd_records = $stock->kd_records()->orderBy('date', 'desc')
                ->where('interval', $interval)
                ->where('fastk_period', $kd_period)
                ->select('stochastic_k', 'stochastic_d', 'date')
                ->take(10)
                ->get()
                ->reverse();

            $latest_kd = $kd_records->last();

            $rsi_records = $stock->rsi_records()->orderBy('date', 'desc')
                ->where('interval', $interval)
                ->where('time_period', $rsi_period)
                ->take(10)
                ->get()
                ->reverse()
                ->pluck('rsi', 'date');

            $stock_position = $stock_positions->where('stock_id', $stock->id)->first();

            $close_price = empty($latest_daily_record->close_price) ? 0.0 : $latest_daily_record->close_price;

            $units = empty($stock_position) ? 0.0 : $stock_position->units;

            //投資總額 = 單位數 * 平均開倉價
            $invested = empty($stock_position->units) || empty($stock_position->avg_open) ? 0.0 : ($stock_position->units * $stock_position->avg_open);

            //市值價格 = 單位數 * 收盤價
            $value_price = $units * $close_price;

            //投資損益 = 市值價格 - 投資總額
            $profit_loss_value = $value_price - $invested;

            $profit_loss_percent = null;

            if ($profit_loss_value + $invested == 0) {

                $profit_loss_percent = 0.0;
            } else {

                if ($invested == 0) {

                    $profit_loss_percent = 0.0;
                } else {

                    $profit_loss_percent = round(($profit_loss_value / $invested) * 100, 2);
                }
            }

            return collect([
                'id' => $stock->id,
                'is_update' => ($latest_trade_date == $latest_daily_record->date) ? true : false,
                'symbol' => $stock->symbol,
                'name' => $stock->name,
                'type' => $stock->type,
                'close_price' => $close_price,
                'change_percent' => round($latest_daily_record->change_percent, 2),
                'stochastic_k' => round(optional($latest_kd)->stochastic_k, 4),
                'stochastic_d' => round(optional($latest_kd)->stochastic_d, 4),
                'kd_diffs' => $kd_records->map(function ($kd_record) {

                    return $kd_record['stochastic_k'] / $kd_record['stochastic_d'];
                }),
                'rsi_records' => $rsi_records,
                'latest_rsi' => $rsi_records->last(),
                'date' => $latest_daily_record->date,
                'units' => $units,
                'avg_open' => empty($stock_position) ? 0.0 : $stock_position->avg_open,
                'invested' => round($invested, 2),
                'profit_loss_percent' => round($profit_loss_percent, 2),
                'profit_loss_value' => round($profit_loss_value, 2),
                'target_position' => empty($stock_position) ? 0.0 : $stock_position->target_position,
            ]);
        });


        if ($select_option_order == 'asc') {
            $stocks = $stocks->sortBy($select_option_column);
        } else {
            $stocks = $stocks->sortByDesc($select_option_column);
        }

        return view('dashboard', [
            'stocks' => $stocks->values(),
            'total_pages' => $total_pages,
            'current_page' => $current_page,
        ]);
    }
}
