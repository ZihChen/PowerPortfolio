<?php

namespace App\Http\Controllers;

use App\Services\FiscalOverviewService;
use App\Services\StockService;
use Illuminate\Http\Request;

class FiscalOverviewController
{
    protected $fiscalOverviewService;

    protected $stockService;

    public function __construct(FiscalOverviewService $fiscalOverviewService,
                                StockService $stockService)
    {
        $this->fiscalOverviewService = $fiscalOverviewService;
        $this->stockService = $stockService;
    }

    public function getStockFiscalOverview(Request $request)
    {
        $stock_id = $request->route('stock_id');

        $stock = $this->stockService->getStockById($stock_id, ['fiscal_overviews' => function ($query) {
            $query->orderBy('latest_refresh', 'desc')
                ->take(4);
        }]);

        return view('overview', [
            'name' => $stock->name,
            'symbol' => $stock->symbol,
            'fiscal_overviews' => $stock->fiscal_overviews
        ]);
    }
}
