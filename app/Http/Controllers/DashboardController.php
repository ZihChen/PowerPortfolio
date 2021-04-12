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

        $stocks = $this->stockService->getStocksByUser($user, ['latest_daily_record']);

        $stock_positions = $this->userStockPositionService->getStockPositionsByUser($user);

        $stocks = $stocks->map(function ($stock) use ($stock_positions) {

            $latest_daily_record = optional($stock->latest_daily_record);

            $stock_position = $stock_positions->where('stock_id', $stock->id)->first();

            return [
                'id' => $stock->id,
                'symbol' => $stock->symbol,
                'name' => $stock->name,
                'type' => $stock->type,
                'close_price' => $latest_daily_record->close_price,
                'change_percent' => $latest_daily_record->change_percent,
                'stochastic_k' => $latest_daily_record->stochastic_k,
                'stochastic_d' => $latest_daily_record->stochastic_d,
                'rsi' => $latest_daily_record->rsi,
                'date' => $latest_daily_record->date,
                'invested' => empty($stock_position) ? 0.0 : $stock_position->invested,
                'target_position' => empty($stock_position) ? 0.0 : $stock_position->target_position,
                'avg_open' => empty($stock_position) ? 0.0 : $stock_position->avg_open,
            ];
        });

        return view('dashboard', ['stocks' => $stocks]);
    }
}
