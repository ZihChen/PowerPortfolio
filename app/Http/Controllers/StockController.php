<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use App\Services\UserStockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $stockService;

    protected $userStockService;

    public function __construct(StockService $stockService,
                                UserStockService $userStockService)
    {
        $this->stockService = $stockService;
        $this->userStockService = $userStockService;
    }

    public function autocompleteSearch(Request $request)
    {
        $result = $this->stockService->getMatchStocksByKeyword($request->get('keyword'));

        return response()->json($result);
    }

    public function postUserStockRelation(Request $request)
    {
        $user = $request->user();

        $symbol = $request->input('symbol');

        $stock = $this->stockService->getStockBySymbol($symbol);

        $this->userStockService->attachStockByUser($user, $stock->id);

        return redirect('/dashboard');
    }
}
