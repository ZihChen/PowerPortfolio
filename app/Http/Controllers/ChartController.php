<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockService;
use App\Services\ChartService;

class ChartController extends Controller
{
    protected $stockService;

    protected $chartService;

    public function __construct(StockService $stockService,
                                ChartService $chartService)
    {
        $this->stockService = $stockService;
        $this->chartService = $chartService;
    }

    public function getQuotesChart(Request $request)
    {
        $stock_id = $request->route('stock_id');

        $stock = $this->stockService->getStockById($stock_id, [
            'daily_records' => function ($query) {

                $query->orderBy('date', 'desc')
                    ->take(30);
            },
            'kd_records' => function ($query) {
                $query->orderBy('date', 'desc')
                    ->take(30);
            },
            'rsi_records' => function ($query) {
                $query->orderBy('date', 'desc')
                    ->take(30);
            },
        ]);

        $daily_records = $stock->daily_records->reverse();

        $kd_records = $stock->kd_records->reverse();

        $rsi_records = $stock->rsi_records->reverse();

        $kd_chart = $this->chartService->renderKDChart($kd_records);

        $quote_chart = $this->chartService->renderQuoteChart($daily_records);

        $rsi_chart = $this->chartService->renderRsiChart($rsi_records);

        return view('chart_page', [
            'kd_chart' => $kd_chart,
            'quote_chart' => $quote_chart,
            'rsi_chart' => $rsi_chart,
        ]);
    }
}
