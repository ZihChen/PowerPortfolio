<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-15
 * Time: 00:02
 */

namespace App\Services;


use App\Models\DailyStockRecord;
use App\Models\RelativeStrengthIndex;
use App\Models\Stock;
use Carbon\Carbon;

class RelativeStrengthIndexService
{
    protected $rsiModel;

    public function __construct(RelativeStrengthIndex $rsiModel)
    {
        $this->rsiModel = $rsiModel;
    }

    /**
     * RSI公式:
     *
     * N = 14
     *
     * step1.
     * 第一筆AvgGain和AvgLoss
     * InitAvgGain = 過去N天的漲幅加總平均
     * InitAvgLoss = 過去N天的跌幅加總平均
     *
     * step2.
     * AvgGain(N) = (AvGain(N - 1) * (N - 1 / N)) + (當日漲幅 * (1 / N))
     * AvgLoss(N) = (AvgLoss(N - 1) * (N - 1/ N)) + (當跌漲幅 * (1 / N))
     *
     * step3.
     * RS = AvgGain(N) / AvgLoss(N)
     * RSI = 100 - ( 100 / (1 + RS))
     *
     * @param $stock
     * @param $rsi_records
     * @param $interval
     * @param $series_type
     * @param $rsi_period
     */
    public function insertRsiIndicatorByDailyRecords($stock, $rsi_records, $interval, $series_type, $rsi_period)
    {
        $now = Carbon::now();

        $daily_records = $stock->daily_records;

        $insert_fields = [];

        $init_avg_gain = 0;
        $init_avg_loss = 0;

        for ($i = 0; $i < ($daily_records->count()); $i++) {

            if ($i == 0) {

                $avg_gain = [];

                $avg_loss = [];

                $period_records = $daily_records->slice($i, $rsi_period)->pluck('close_price');

                for ($i = 13; $i >= 1; $i--) {

                    $diff_value = $period_records[$i] - $period_records[$i - 1];

                    if ($diff_value > 0) {

                        $avg_gain[] = $diff_value;

                    } elseif ($diff_value == 0) {

                        continue;

                    } else {

                        $avg_loss[] = $diff_value;

                    }
                }

                $init_avg_gain = array_sum($avg_gain) / $rsi_period;

                $init_avg_loss = array_sum($avg_loss) / $rsi_period;

                $i = 12;

                continue;
            }

            if ($i == 13) {

                $insert_fields[] = [
                    'date' => $daily_records[$i]['date'],
                    'stock_id' => $stock->id,
                    'record_id' => optional($daily_records[$i])->id,
                    'rsi' => $rsi_records[$daily_records[$i]['date']]['rsi'],
                    'avg_gain' => $init_avg_gain,
                    'avg_loss' => $init_avg_loss,
                    'interval' => $interval,
                    'series_type' => $series_type,
                    'time_period' => $rsi_period,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                continue;
            }

            if ($i == 14) {

                $change = $daily_records[$i]->close_price - $daily_records[$i - 1]->close_price;

                list($price_up, $price_fall) = $this->getClosePriceChange($change);

                $insert_fields[] = [
                    'date' => $daily_records[$i]['date'],
                    'stock_id' => $stock->id,
                    'record_id' => optional($daily_records[$i])->id,
                    'rsi' => $rsi_records[$daily_records[$i]['date']]['rsi'],
                    'avg_gain' => ($init_avg_gain * (1 - (1 / $rsi_period)) + ($price_up * (1 / $rsi_period))),
                    'avg_loss' => ($init_avg_loss * (1 - (1 / $rsi_period)) + ($price_fall * (1 / $rsi_period))),
                    'interval' => $interval,
                    'series_type' => $series_type,
                    'time_period' => $rsi_period,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                continue;
            }

            $change = $daily_records[$i]->close_price - $daily_records[$i - 1]->close_price;

            list($price_up, $price_fall) = $this->getClosePriceChange($change);

            $insert_fields[] = [
                'date' => $daily_records[$i]['date'],
                'stock_id' => $stock->id,
                'record_id' => optional($daily_records[$i])->id,
                'rsi' => $rsi_records[$daily_records[$i]['date']]['rsi'],
                'avg_gain' => (last($insert_fields)['avg_gain'] * (1 - (1 / $rsi_period)) + ($price_up * (1 / $rsi_period))),
                'avg_loss' => (last($insert_fields)['avg_loss'] * (1 - (1 / $rsi_period)) + ($price_fall * (1 / $rsi_period))),
                'interval' => $interval,
                'series_type' => $series_type,
                'time_period' => $rsi_period,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->rsiModel->insert($insert_fields);
    }

    private function getClosePriceChange($change)
    {
        if ($change > 0) {

            $price_up = $change;
            $price_fall = 0;

        } elseif ($change == 0) {

            $price_up = 0;
            $price_fall = 0;

        } else {

            $price_up = 0;
            $price_fall = $change;
        }

        return [
            $price_up,
            $price_fall,
        ];
    }

    /**
     * 計算當日RSI指標
     * AvgGain(N) = (AvGain(N - 1) * (N - 1 / N)) + (當日漲幅 * (1 / N))
     * AvgLoss(N) = (AvgLoss(N - 1) * (N - 1/ N)) + (當跌漲幅 * (1 / N))
     *
     * step3.
     * RS = AvgGain(N) / AvgLoss(N)
     * RSI = 100 - ( 100 / (1 + RS))
     *
     * @param $stock
     * @param $latest_quote
     * @param $rsi_period
     * @return array
     */
    public function calculateRSI($stock, $latest_quote, $rsi_period)
    {
        $previous_close_price = ($stock->daily_records->first())->close_price;

        $change = $latest_quote['close_price'] - $previous_close_price;

        list($price_up, $price_fall) = $this->getClosePriceChange($change);

        $rsi_record = $stock->rsi_records->first();

        $previous_avg_gain = $rsi_record->avg_gain;

        $previous_avg_loss = $rsi_record->avg_loss;

        $avg_gain = ($previous_avg_gain * (($rsi_period - 1) / $rsi_period)) + ($price_up * (1 / $rsi_period));

        $avg_loss = ($previous_avg_loss * (($rsi_period - 1) / $rsi_period)) + ($price_fall * (1 / $rsi_period));

        $rs = abs($avg_gain / $avg_loss);

        $rsi = 100 - (100 / (1 + $rs));

        return [
            'rsi' => $rsi,
            'avg_gain' => $avg_gain,
            'avg_loss' => $avg_loss,
        ];
    }

    public function calculateRsiByStock($stock, $rsi_period)
    {
        $latest_close_price = $stock->daily_records->first()->close_price;

        $previous_close_price = $stock->daily_records->get(1)->close_price;

        $change = $latest_close_price - $previous_close_price;

        list($price_up, $price_fall) = $this->getClosePriceChange($change);

        $rsi_record = $stock->rsi_records->first();

        $previous_avg_gain = $rsi_record->avg_gain;

        $previous_avg_loss = $rsi_record->avg_loss;

        $avg_gain = ($previous_avg_gain * (($rsi_period - 1) / $rsi_period)) + ($price_up * (1 / $rsi_period));

        $avg_loss = ($previous_avg_loss * (($rsi_period - 1) / $rsi_period)) + ($price_fall * (1 / $rsi_period));

        $rs = abs($avg_gain / $avg_loss);

        $rsi = 100 - (100 / (1 + $rs));

        return [
            'rsi' => $rsi,
            'avg_gain' => $avg_gain,
            'avg_loss' => $avg_loss,
        ];
    }

    /**
     * @param Stock $stock => 股票
     * @param DailyStockRecord $daily_record => 當日收盤股價
     * @param array $rsi_indicator => 計算當日的AvgGain、AvgLoss、RSI
     * @param string $interval => default:daily
     * @param int $rsi_period => default:14
     * @return mixed
     */
    public function firstOrCreateRSIRecordByStock($stock, $daily_record, $rsi_indicator, $interval = 'daily', $rsi_period = 14)
    {
        return $stock->rsi_records()->firstOrCreate([
            'date' => $daily_record['date']
        ], [
            'record_id' => $daily_record['id'],
            'date' => $daily_record['date'],
            'series_type' => 'close',
            'interval' => $interval,
            'time_period' => $rsi_period,
            'avg_gain' => $rsi_indicator['avg_gain'],
            'avg_loss' => $rsi_indicator['avg_loss'],
            'rsi' => $rsi_indicator['rsi'],
        ]);
    }

    /**
     * RSI50目標價
     * 目標價 = Close Price(yesterday) - (13 * AvgGain(yesterday)) - (13 * AvgLoss(yesterday))
     * @param $latest_daily_record
     * @param $latest_rsi_record
     * @return float|int
     */
    public function calculateRsiFiftyTargetPrice($latest_daily_record, $latest_rsi_record)
    {
        $target_price = $latest_daily_record->close_price
            - (13 * $latest_rsi_record->avg_gain)
            - (13 * $latest_rsi_record->avg_loss);

        return round($target_price, 2);
    }

    /**
     * RSI30目標價
     * 目標價 = Close Price(yesterday)  - (13 * AvgGain(yesterday)) - ((39/7) * AvgLoss(yesterday))
     * @param $latest_daily_record
     * @param $latest_rsi_record
     * @return float
     */
    public function calculateRsiThirtyTargetPrice($latest_daily_record, $latest_rsi_record)
    {
        $target_price = $latest_daily_record->close_price
            - (13 * $latest_rsi_record->avg_gain)
            - ((39/7) * $latest_rsi_record->avg_loss);

        return round($target_price, 2);
    }
}
