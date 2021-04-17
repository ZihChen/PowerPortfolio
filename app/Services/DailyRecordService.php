<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-05
 * Time: 00:54
 */

namespace App\Services;


use App\Models\DailyStockRecord;
use Carbon\Carbon;

class DailyRecordService
{
    protected $dailyRecordModel;

    public function __construct(DailyStockRecord $dailyRecordModel)
    {
        $this->dailyRecordModel = $dailyRecordModel;
    }

    public function firstOrCreateDailyRecordByStock($stock, $data)
    {
        return $stock->daily_records()->firstOrCreate([
            'date' => $data['date'],
        ], [
            'date' => $data['date'],
            'close_price' => $data['close_price'],
            'low_price' => $data['low_price'],
            'high_price' => $data['high_price'],
            'change_percent' => (float)$data['change_percent'],
            'rsv' => $data['rsv'],
            'stochastic_k' => $data['stochastic_k'],
            'stochastic_d' => $data['stochastic_d'],
            'rsi' => $data['rsi'],
        ]);
    }

    public function insertDailyRecordsByStock($stock, $daily_records)
    {
        $now = Carbon::now();

        $records_total = count($daily_records);

        $is_write_change_percent = true;

        $insert_fields = [];

        foreach ($daily_records as $key => $daily_record) {

            if ($key == $records_total - 1) $is_write_change_percent = false;   //最後一筆因為沒有前一筆資料，所以change_percent不做更新

            $insert_fields[] = [
                'stock_id' => $stock->id,
                'date' => $daily_record['date'],
                'close_price' => $daily_record['close_price'],
                'low_price' => $daily_record['low_price'],
                'high_price' => $daily_record['high_price'],
                'change_percent' => $is_write_change_percent ? round((($daily_records[$key]['close_price'] - $daily_records[$key + 1]['close_price'])
                        / $daily_records[$key + 1]['close_price']) * 100, 4) : 0.0,    //公式:當日收盤 - 昨日收盤 / 昨日收盤
                'created_at' => $now,
                'updated_at' => $now,
            ];

        }

        $this->dailyRecordModel->insert(array_reverse($insert_fields));
    }
}
