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

    /**
     * 更新最後三筆DailyRecords的RSV，用於計算KD
     * @param $stock
     */
    public function calculateRsvAndUpdateLatestThreeRecords($stock)
    {
        for ($i = 0; $i <= 2; $i++) {

            $daily_records = $stock->daily_records()
                ->skip($i)
                ->take(9)
                ->get();

            //要被更新的DailyRecord
            $daily_record = $daily_records->first();

            $highest_price = $daily_records->max('high_price');

            $lowest_price = $daily_records->min('low_price');

            $rsv = ($daily_record->close_price - $lowest_price) / ($highest_price - $lowest_price);

            $daily_record->update(['rsv' => $rsv]);
        }
    }

    /**
     * 計算當日的KD值
     * ex. fast-k:9,slow-k:3,slow-d:3
     * 公式:
     * K = ((當日RSV + 前2日RSV) / 3) * 100
     * D = (當日K + 前2日K) / 3
     * RSV = (當日收盤價 - 9日內最低價) / (9日內最高價 - 9日內最低價)
     *
     * @param $stock
     * @param $latest_quote
     * @return array
     */
    public function calculateStochasticOscillator($stock, $latest_quote)
    {
        $daily_records = $stock->daily_records()->take(8)->get();

        $highest_price = $daily_records->max('high_price');

        if ($latest_quote['high_price'] > $highest_price) $highest_price = $latest_quote['high_price'];

        $lowest_price = $daily_records->min('low_price');

        if ($latest_quote['low_price'] < $lowest_price) $lowest_price = $latest_quote['low_price'];

        $rsv = ($latest_quote['close_price'] - $lowest_price) / ($highest_price - $lowest_price);

        $latest_two_records = $daily_records->take(2);

        $stochastic_k = ($latest_two_records->pluck('rsv')->push($rsv)->avg() * 100);

        $stochastic_d = $latest_two_records->pluck('stochastic_k')->push($stochastic_k)->avg();

        return [
            'rsv' => $rsv,
            'stochastic_k' => $stochastic_k,
            'stochastic_d' => $stochastic_d,
        ];
    }

    /**
     * 計算RSI指標
     * 公式:
     * RSI = 100 - ( 100 / (1 + RS) )
     * RS = avg_gain(N days) / avg_loss(N days)
     *
     * @param $stock
     * @param $latest_quote
     * @return array
     */
    public function calculateRSI($stock, $latest_quote)
    {
        $daily_records = $stock->daily_records()->take(14)->get();

        $close_price_collect = ($daily_records->pluck('close_price')->reverse()->push($latest_quote['close_price']))->values();

        $close_price_arr = $close_price_collect->toArray();

        $avg_gain = [];

        $avg_loss = [];

        for ($count = 0; $count < 15; $count++) {

            if ($count == 0) continue;

            $diff_value = $close_price_arr[$count] - $close_price_arr[$count - 1];

            if ($diff_value > 0) {

                $avg_gain[] = $diff_value;

            } elseif ($diff_value == 0) {

                continue;

            } else {

                $avg_loss[] = $diff_value;

            }
        }

        $rs = abs(array_sum($avg_gain) / array_sum($avg_loss));

        $rsi = 100 - (100 / (1 + $rs));

        return [
            'rsi' => $rsi,
        ];
    }
}
