<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-05
 * Time: 01:10
 */

namespace App\Services;


class FiscalOverviewService
{
    public function firstOrCreateFiscalOverview($stock, $data)
    {
        return $stock->fiscal_overviews()->firstOrCreate([
            'latest_refresh' => $data['latest_refresh']
        ], [
            'latest_refresh' => $data['latest_refresh'],
            'eps' => $data['eps'],
            'pe_ratio' => $data['pe_ratio'],
            'roa_ttm' => $data['roa_ttm'],
            'roe_ttm' => $data['roe_ttm'],
            'profit_margin' => $data['profit_margin'],
            'operating_margin' => $data['operating_margin'],
            'ev_to_revenue' => $data['ev_to_revenue'],
            'content' => $data['content'],
        ]);
    }
}
