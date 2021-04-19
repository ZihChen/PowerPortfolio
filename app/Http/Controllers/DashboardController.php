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

class DashboardController
{
    protected $stockService;

    protected $userStockPositionService;

    public function __construct(StockService $stockService,
                                UserStockPositionService $userStockPositionService)
    {
        $this->stockService = $stockService;
        $this->userStockPositionService = $userStockPositionService;
    }

    public function getDashboard(Request $request)
    {
        $user = $request->user();

        $interval = $request->get('interval', 'daily');

        $kd_period = $request->get('kd_period', 9);

        $rsi_period = $request->get('rsi_period', 14);

        $stocks = $this->stockService->getStocksByUser($user, [
            'latest_daily_record'
        ]);

        $stock_positions = $this->userStockPositionService->getStockPositionsByUser($user);

        $stocks = $stocks->map(function ($stock) use ($stock_positions, $interval, $kd_period, $rsi_period) {

            $latest_daily_record = optional($stock->latest_daily_record);

            $kd_record = $stock->kd_records()->orderBy('date', 'desc')
                ->where('interval', $interval)
                ->where('fastk_period', $kd_period)
                ->take(1)
                ->first();

            $rsi_record = $stock->rsi_records()->orderBy('date', 'desc')
                ->where('interval', $interval)
                ->where('time_period', $rsi_period)
                ->take(1)
                ->first();

            $stock_position = $stock_positions->where('stock_id', $stock->id)->first();

            $close_price = empty($latest_daily_record->close_price) ? 0.0 : $latest_daily_record->close_price;

            $units = empty($stock_position) ? 0.0 : $stock_position->units;

            $invested = empty($stock_position->units) || empty($stock_position->avg_open) ? 0.0 : ($stock_position->units * $stock_position->avg_open);

            $value_price = $units * $close_price;

            $profit_loss_value = $value_price - $invested;

            $profit_loss_percent = null;

            if ($profit_loss_value + $invested == 0) {

                $profit_loss_percent = 0.0;
            } else {

                $profit_loss_percent = round(($profit_loss_value / $invested) * 100, 2);
            }

            return [
                'id' => $stock->id,
                'symbol' => $stock->symbol,
                'name' => $stock->name,
                'type' => $stock->type,
                'close_price' => $close_price,
                'change_percent' => $latest_daily_record->change_percent,
                'stochastic_k' => optional($kd_record)->stochastic_k,
                'stochastic_d' => optional($kd_record)->stochastic_d,
                'rsi' => optional($rsi_record)->rsi,
                'date' => $latest_daily_record->date,
                'units' => $units,
                'avg_open' => empty($stock_position) ? 0.0 : $stock_position->avg_open,
                'invested' => $invested,
                'profit_loss_percent' => $profit_loss_percent,
                'profit_loss_value' => $profit_loss_value,
                'target_position' => empty($stock_position) ? 0.0 : $stock_position->target_position,
            ];
        });

        return view('dashboard', ['stocks' => $stocks]);
    }
}
