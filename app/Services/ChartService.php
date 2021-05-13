<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-05-14
 * Time: 00:00
 */

namespace App\Services;


use App\Charts\SimpleChart;

class ChartService
{
    public function renderQuoteChart($daily_records)
    {
        $labels = $daily_records->pluck('date');

        $quotes = $daily_records->pluck('close_price');

        $chart = new SimpleChart();

        $chart->title('股價');

        $chart->labels($labels);

        $chart->dataset('price', 'line', $quotes)
            ->backgroundColor('#fd7e14')
            ->fill(false);

        return $chart;
    }

    public function renderKDChart($kd_records)
    {
        $labels = $kd_records->pluck('date');

        $stochastic_k = $kd_records->pluck('stochastic_k');

        $stochastic_d = $kd_records->pluck('stochastic_d');

        $chart = new SimpleChart();

        $chart->title('KD');

        $chart->labels($labels);

        $chart->dataset('K', 'line', $stochastic_k)
            ->backgroundColor('#14B45A')
            ->fill(false);
        $chart->dataset('D', 'line', $stochastic_d)
            ->backgroundColor('#FF6E6E')
            ->fill(false);

        return $chart;
    }

    public function renderRsiChart($rsi_records)
    {
        $labels = $rsi_records->pluck('date');

        $rsi = $rsi_records->pluck('rsi');

        $chart = new SimpleChart();

        $chart->title('強弱指標');

        $chart->labels($labels);

        $chart->dataset('RSI', 'line', $rsi)
            ->backgroundColor('#218BC3')
            ->fill(false);

        return $chart;
    }
}
