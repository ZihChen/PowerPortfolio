<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-02
 * Time: 01:24
 */

namespace App\Services;

use App\Models\Stock;
use Carbon\Carbon;

class StockService
{
    protected $stockModel;

    public function __construct(Stock $stockModel)
    {
        $this->stockModel = $stockModel;
    }

    public function firstOrCreateStock($data)
    {
        $now_date = (Carbon::now())->toDate();

        return $this->stockModel->firstOrCreate([
            'symbol' => $data['symbol']
        ], [
            'symbol' => $data['symbol'],
            'name' => $data['name'],
            'type' => $data['type'],
            'sector' => $data['sector'],
            'industry' => $data['industry'],
            'quote_latest_refresh' => isset($data['quote_latest_refresh']) ? $data['quote_latest_refresh'] : $now_date,
            'fiscal_latest_refresh' => isset($data['fiscal_latest_refresh']) ? $data['fiscal_latest_refresh'] : $now_date,
        ]);
    }

    public function updateStockRefreshDate($stock, $quote_latest_refresh, $fiscal_latest_refresh)
    {
        return $stock->update([
            'quote_latest_refresh' => $quote_latest_refresh,
            'fiscal_latest_refresh' => $fiscal_latest_refresh,
        ]);
    }

    public function getStocksByUser($user, $relation_fields = [])
    {
        return $user->stocks()->with($relation_fields)->get();
    }

    public function getMatchStocksByKeyword($keyword)
    {
        return $this->stockModel->where('symbol', 'like', $keyword . '%')
            ->orWhere('name', 'like', $keyword . '%')
            ->get();
    }

    public function getStockById($stock_id, $relation_fields = [])
    {
        return $this->stockModel->with($relation_fields)->find($stock_id);
    }

    public function getStockBySymbol($symbol, $relation_fields = [])
    {
        return $this->stockModel->with($relation_fields)->where('symbol', $symbol)->first();
    }

    public function getStocksByPaginate($user, $page, $limit, $relation_fields = [])
    {
        return $user->stocks()->with($relation_fields)
            ->offset($limit * ($page - 1))
            ->limit($limit)
            ->get();
    }
}
