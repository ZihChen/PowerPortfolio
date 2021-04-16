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

    public function insertKdIndicatorByDailyRecords($daily_records, $kd_records, $interval, $kd_period)
    {
        $now = Carbon::now();

        $rsv_arr = [];

        for ($i = 0; $i < ($daily_records->count() - $kd_period); $i++) {

            $period_records = $daily_records->slice($i, $kd_period);

            $highest_price = $period_records->max('high_price');

            $lowest_price = $period_records->min('low_price');

            $rsv = ($daily_records[$i]->close_price - $lowest_price) / ($highest_price - $lowest_price);

            $rsv_arr[$daily_records[$i]->date] = [
                'rsv' => $rsv,
            ];
        }

        $insert_fields = [];

        foreach ($kd_records as $kd_record) {

            $insert_fields[] = [
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
}
