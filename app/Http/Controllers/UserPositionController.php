<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockService;
use App\Services\UserPositionService;

class UserPositionController extends Controller
{
    protected $stockService;

    protected $userPositionService;

    /**
     * UserPositionController constructor.
     * @param StockService $stockService
     * @param UserPositionService $userPositionService
     */
    public function __construct(StockService $stockService,
                                UserPositionService $userPositionService)
    {
        $this->stockService = $stockService;
        $this->userPositionService = $userPositionService;
    }

    public function postUserPosition(Request $request)
    {
        $user = $request->user();

        $stock_id = $request->route('stock_id');

        $stock = $this->stockService->getStockById($stock_id);

        $input = $request->only(['units', 'target_position', 'avg_open']);

        $this->userPositionService->updateOrCreateUserPosition($user, $stock, $input);

        return response()->json([
            'msg' => 'success',
            'status' => 200
        ]);
    }
}
