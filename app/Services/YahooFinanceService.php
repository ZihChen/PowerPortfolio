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

    public function getQuotesBySymbol($symbols)
    {

    }
}
