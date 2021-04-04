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
            'overview_latest_refresh' => isset($data['overview_latest_refresh']) ? $data['overview_latest_refresh'] : $now_date,
        ]);
    }
}
