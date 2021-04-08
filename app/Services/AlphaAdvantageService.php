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

    public function searchStockInfo($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::SYMBOL_SEARCH, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (empty($response['bestMatches'])) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        $response = $response['bestMatches'];

        return array_map(function ($item) {

            return [
                'symbol' => $item['1. symbol'],
                'name' => $item['2. name'],
                'type' => $item['3. type'],
            ];

        }, $response);
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

        $response = $response['Global Quote'];

        return [
            'symbol' => $response['01. symbol'],
            'close_price' => round($response['05. price'], 4),
            'high_price' => round($response['03. high'], 4),
            'low_price' => round($response['04. low'], 4),
            'change_percent' => round($response['10. change percent'], 4),
            'date' => $response['07. latest trading day'],
        ];
    }

    public function getStockRsiIndicator($symbol)
    {
        $response = $this->alphaAdvantage->callAPIByFunction(AlphaAdvantageAPI::RSI, $symbol);

        if (isset($response['Note'])) throw new HttpResponseException(response($this->highFrequencyRequestMsg, $this->accepted));

        if (empty($response)) throw new HttpResponseException(response($this->symbolNotFoundMsg, $this->notFound));

        $response = $response['Technical Analysis: RSI'];

        return array_map(function ($record) {

            return [
                'rsi' => round($record['RSI'], 4)
            ];

        }, array_slice($response, 0, 15));
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
            'eps' => round($response['EPS'], 4),
            'pe_ratio' => round($response['PERatio'], 4),
            'roa_ttm' => round($response['ReturnOnAssetsTTM'], 4),
            'roe_ttm' => round($response['ReturnOnEquityTTM'], 4),
            'profit_margin' => round($response['ProfitMargin'], 4),
            'operating_margin' => round($response['OperatingMarginTTM'], 4),
            'ev_to_revenue' => round($response['EVToRevenue'], 4),
            'content' => json_encode($response),
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
                'open_price' => round($response[$date]['1. open'], 4),
                'low_price' => round($response[$date]['3. low'], 4),
                'high_price' => round($response[$date]['2. high'], 4),
                'close_price' => round($response[$date]['4. close'], 4),
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
                    'stochastic_k' => round($response[$date]['SlowK'], 4),
                    'stochastic_d' => round($response[$date]['SlowD'], 4),
                ];

                $flag++;
            }
        }

        return $filter_fields;
    }
}
