<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-23
 * Time: 14:46
 */

namespace App\ThirdPartyAPI;

use GuzzleHttp\Client;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class YahooFinanceAPI
{
    private $client;

    public function __construct()
    {
        $guzzleClient = new Client();
        $this->client = ApiClientFactory::createApiClient($guzzleClient);
    }

    public function getSingleQuote($symbol)
    {
        return $this->client->getQuote($symbol);
    }

    public function getMultiQuote($symbols = [])
    {
        return $this->client->getQuotes($symbols);
    }
}
