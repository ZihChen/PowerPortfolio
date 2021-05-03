<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-23
 * Time: 14:58
 */

namespace App\Services;


use App\ThirdPartyAPI\YahooFinanceAPI;
use Carbon\Carbon;

class YahooFinanceService
{
    protected $yahooFinance;

    public function __construct(YahooFinanceAPI $yahooFinance)
    {
        $this->yahooFinance = $yahooFinance;
    }

    public function getLatestTradeDate()
    {
        $response = $this->yahooFinance->getSingleQuote('SPY');

        $result = json_decode(json_encode($response), true);

        $date = explode(" ", $result['regularMarketTime']['date']);

        return $date[0];
    }

    public function getQuoteBySymbol($symbol)
    {
        $response = $this->yahooFinance->getSingleQuote($symbol);

        $result = json_decode(json_encode($response), true);

        $date = explode(" ", $result['regularMarketTime']['date']);

        return [
            'close_price' => $result['regularMarketPrice'],
            'date' => $date[0],
        ];
    }

    public function getQuotesBySymbol($stocks)
    {
        $now = Carbon::now();

        $symbols = $stocks->pluck('symbol')->toArray();

        $response = $this->yahooFinance->getMultiQuote($symbols);

        $result = json_decode(json_encode($response), true);

        $data = [];

        foreach ($stocks as $key => $stock) {

            $date = explode(" ", $result[$key]['regularMarketTime']['date']);

            $data[$stock->symbol] = [
                'stock_id' => $stock->id,
                'date' => $date[0],
                'close_price' => $result[$key]['regularMarketPrice'],
                'high_price' => $result[$key]['regularMarketDayHigh'],
                'low_price' => $result[$key]['regularMarketDayLow'],
                'change_percent' => $result[$key]['regularMarketChangePercent'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

        }

        return $data;
    }
}
