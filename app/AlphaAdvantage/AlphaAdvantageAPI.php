<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-30
 * Time: 21:32
 */

namespace App\AlphaAdvantage;


use GuzzleHttp\Client;

class AlphaAdvantageAPI
{
    const SYMBOL_SEARCH = 'SYMBOL_SEARCH'; //搜尋標的
    const OVERVIEW = 'OVERVIEW';    //個股基本面資料
    const GLOBAL_QUOTE = 'GLOBAL_QUOTE';    //個股當日收盤詳細資料
    const TIME_SERIES_DAILY = 'TIME_SERIES_DAILY';  //個股歷史股價紀錄
    const STOCH = 'STOCH';  //KD指標
    const RSI = 'RSI';  //RSI指標

    private $url;
    private $client;

    public function __construct()
    {
        $this->url = "https://www.alphavantage.co/query?apikey=" . env('ALPHA_VANTAGE_KEY') . '&';
        $this->client = new Client();
    }

    public function callAPIByFunction($method, $symbol, $interval = null, $period = null, $series_type = null)
    {

        switch ($method) {

            //搜尋股票代號、基本資料
            case self::SYMBOL_SEARCH:

                $url = $this->url . "function=" . self::SYMBOL_SEARCH . "&keywords=" . $symbol;

                break;

            //取得最新報價資訊(單筆)
            case self::GLOBAL_QUOTE:

                $url = $this->url . "function=" . self::GLOBAL_QUOTE . "&symbol=" . $symbol;

                break;

            //取得最新基本面(單筆)
            case self::OVERVIEW:

                $url = $this->url . "function=" . self::OVERVIEW . "&symbol=" . $symbol;

                break;

            //取得歷史報價紀錄(多筆)
            case self::TIME_SERIES_DAILY:

                $url = $this->url . "function=" . self::TIME_SERIES_DAILY . "&symbol=" . $symbol;

                break;

            //取得歷史kd值(多筆)
            case self::STOCH:

                $interval = empty($interval) ? 'daily' : $interval;

                $period = empty($period) ? '9' : $period;

                $url = $this->url . "function=" . self::STOCH . "&interval=$interval" . "&fastkperiod=$period" . "&symbol=" . $symbol;

                break;

            //RSI指標(多筆)
            case self::RSI:

                $interval = empty($interval) ? 'daily' : $interval;

                $period = empty($period) ? '14' : $period;

                $series_type = empty($series_type) ? 'close' : $series_type;

                $url = $this->url . "function=" . self::RSI . "&interval=$interval" . "&time_period=$period" . "&series_type=close" . "&symbol=" . $symbol. "&series_type=" . $series_type;

                break;
        }

        $res = $this->client->request('GET', $url);

        return json_decode($res->getBody()->getContents(), true);
    }
}
