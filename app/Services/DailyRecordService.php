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
use Carbon\CarbonPeriod;

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

    public function insertDailyRecordsByStock($stock, $daily_records, $kd_records, $rsi_records)
    {
        $now = Carbon::now();

        $now_date = $now->toDateString();

        $now_carbon = clone ($now);

        $before_one_months = ($now->addMonth(-1))->toDateString();

        $period = array_reverse(CarbonPeriod::create($before_one_months, $now_date)->toArray());

        $daily_records_value = array_values($daily_records);

        $insert_fields = [];

        $flag = 0;

        foreach ($period as $date) {

            if ($flag == 15) break;

            $date = $date->format('Y-m-d');

            if (in_array($date, array_keys($daily_records))) {

                $insert_fields[] = [
                    'date' => $date,
                    'close_price' => $daily_records[$date]['close_price'],
                    'low_price' => $daily_records[$date]['low_price'],
                    'high_price' => $daily_records[$date]['high_price'],
                    'change_percent' => round((($daily_records_value[$flag]['close_price'] - $daily_records_value[$flag + 1]['close_price']) / $daily_records_value[$flag + 1]['close_price']) * 100, 2),    //公式:當日收盤 - 昨日收盤 / 昨日收盤
                    'rsv' => 0.0,
                    'stochastic_k' => $kd_records[$date]['stochastic_k'],
                    'stochastic_d' => $kd_records[$date]['stochastic_d'],
                    'rsi' => $rsi_records[$date]['rsi'],
                    'stock_id' => $stock->id,
                    'created_at' => $now_carbon,
                    'updated_at' => $now_carbon,
                ];

                $flag++;
            }
        }

        $this->dailyRecordModel->insert($insert_fields);
    }

    /**
     * 更新最後三筆DailyRecords的RSV
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
     * @param $stock
     * @param $res
     * @return array
     */
    public function calculateStochasticOscillatorAndUpdate($stock, $res)
    {
        $daily_records = $stock->daily_records()->take(8)->get();

        $highest_price = $daily_records->max('high_price');

        if ($res['high_price'] > $highest_price) $highest_price = $res['high_price'];

        $lowest_price = $daily_records->min('low_price');

        if ($res['low_price'] < $lowest_price) $lowest_price = $res['low_price'];

        $rsv = ($res['close_price'] - $lowest_price) / ($highest_price - $lowest_price);

        $latest_two_records = $daily_records->take(2);

        $stochastic_k = ($latest_two_records->pluck('rsv')->push($rsv)->avg() * 100);

        $stochastic_d = $latest_two_records->pluck('stochastic_k')->push($stochastic_k)->avg();

        return [
            'rsv' => $rsv,
            'stochastic_k' => $stochastic_k,
            'stochastic_d' => $stochastic_d,
        ];
    }
}
