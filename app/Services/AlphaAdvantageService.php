<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-30
 * Time: 22:10
 */

namespace App\Services;


use App\AlphaAdvantage\AlphaAdvantageAPI;
use App\Traits\ErrorResponseCodeTrait;
use App\Traits\ErrorResponseMsgTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlphaAdvantageService
{
    use ErrorResponseMsgTrait, ErrorResponseCodeTrait;

    protected $alphaAdvantage;

    public function __construct(AlphaAdvantageAPI $alphaAdvantage)
    {
        $this->alphaAdvantage = $alphaAdvantage;
    }

    /**
     * @param $symbol
     * @return array
     */
    public function getStockLatestQuote($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::GLOBAL_QUOTE, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (empty($response)) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        return [
            'symbol' => $response['Global Quote']['01. symbol'],
            'close_price' => $response['Global Quote']['05. price'],
            'high_price' => $response['Global Quote']['03. high'],
            'low_price' => $response['Global Quote']['04. low'],
            'change_percent' => $response['Global Quote']['10. change percent'],
            'date' => $response['Global Quote']['07. latest trading day'],
        ];
    }

    /**
     * @param $symbol
     * @return array
     */
    public function getStockOverview($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::OVERVIEW, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (empty($response)) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        return [
            'symbol' => $response['Symbol'],
            'name' => $response['Name'],
            'sector' => $response['Sector'],
            'industry' => $response['Industry'],
            'latest_refresh' => $response['LatestQuarter'],
            'eps' => $response['EPS'],
            'pe_ratio' => $response['PERatio'],
            'roa_ttm' => $response['ReturnOnAssetsTTM'],
            'roe_ttm' => $response['ReturnOnEquityTTM'],
            'profit_margin' => $response['ProfitMargin'],
            'operating_margin' => $response['OperatingMarginTTM'],
            'ev_to_revenue' => $response['EVToRevenue'],
        ];
    }

    public function getDailyStockRecords($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::TIME_SERIES_DAILY, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (isset($response['Error Message'])) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        $response = $response['Time Series (Daily)'];

        $now = Carbon::now();

        $now_date = $now->toDateString();

        $before_one_months = ($now->addMonth(-1))->toDateString();

        $period = array_reverse(CarbonPeriod::create($before_one_months, $now_date)->toArray());

        $flag = 0;

        $filter_fields = [];

        foreach ($period as $date) {

            if ($flag == 15) break;

            $date = $date->format('Y-m-d');

            if (empty($response[$date])) continue;

            $filter_fields[$date] = [
                'open_price' => $response[$date]['1. open'],
                'low_price' => $response[$date]['3. low'],
                'high_price' => $response[$date]['2. high'],
                'close_price' => $response[$date]['4. close'],
            ];
        }

        return $filter_fields;
    }

    public function getStockKDIndicatorRecords($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::STOCH, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (empty($response)) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        $response = $response['Technical Analysis: STOCH'];

        $now = Carbon::now();

        $now_date = $now->toDateString();

        $before_two_months = (clone ($now)->addMonth(-2))->toDateString();

        $period = array_reverse(CarbonPeriod::create($before_two_months, $now_date)->toArray());

        $filter_fields = [];

        $flag = 0;

        foreach ($period as $date) {

            if ($flag == 15) break;

            $date = $date->format('Y-m-d');

            if (empty($response[$date])) continue;

            if (in_array($date, array_keys($response))) {

                $filter_fields[$date] = [
                    'stochastic_k' => $response[$date]['SlowK'],
                    'stochastic_d' => $response[$date]['SlowD'],
                ];

                $flag++;
            }
        }

        return $filter_fields;
    }
}
