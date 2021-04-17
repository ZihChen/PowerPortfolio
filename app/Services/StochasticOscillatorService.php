<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-14
 * Time: 00:17
 */

namespace App\Services;


use App\Models\StochasticOscillator;
use Carbon\Carbon;

class StochasticOscillatorService
{
    protected $kdModel;

    public function __construct(StochasticOscillator $kdModel)
    {
        $this->kdModel = $kdModel;
    }

    public function insertKdIndicatorByDailyRecords($stock, $kd_records, $interval, $kd_period)
    {
        $now = Carbon::now();

        $daily_records = $stock->daily_records;

        $rsv_arr = [];

        for ($i = 0; $i < ($daily_records->count() - ($kd_period - 1)); $i++) {

            $target_daily_record = $daily_records[$i + ($kd_period - 1)];

            $period_records = $daily_records->slice($i, $kd_period);

            $highest_price = $period_records->max('high_price');

            if ($target_daily_record['high_price'] > $highest_price) $highest_price = $target_daily_record['high_price'];

            $lowest_price = $period_records->min('low_price');

            if ($target_daily_record['low_price'] < $lowest_price) $lowest_price = $target_daily_record['low_price'];

            $rsv = ($target_daily_record->close_price - $lowest_price) / ($highest_price - $lowest_price);

            $rsv_arr[$target_daily_record->date] = [
                'rsv' => $rsv,
            ];
        }

        $insert_fields = [];

        foreach ($kd_records as $kd_record) {

            $insert_fields[] = [
                'stock_id' => $stock->id,
                'record_id' => optional($daily_records->where('date', $kd_record['date'])->first())->id,
                'date' => $kd_record['date'],
                'interval' => $interval,
                'fastk_period' => $kd_period,
                'slowk_period' => 3,
                'slowd_period' => 3,
                'rsv' => empty($rsv_arr[$kd_record['date']]) ? 0.0 : $rsv_arr[$kd_record['date']]['rsv'],
                'stochastic_k' => $kd_record['stochastic_k'],
                'stochastic_d' => $kd_record['stochastic_d'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->kdModel->insert(array_reverse($insert_fields));
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
}
