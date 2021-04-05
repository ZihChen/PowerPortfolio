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
            'eps' => round($data['eps'], 2),
            'pe_ratio' => round($data['pe_ratio'], 2),
            'roa_ttm' => round($data['roa_ttm'], 2),
            'roe_ttm' => round($data['roe_ttm'], 2),
            'profit_margin' => $data['profit_margin'],
            'operating_margin' => $data['operating_margin'],
            'ev_to_revenue' => $data['ev_to_revenue'],
            'content' => $data['content'],
        ]);
    }
}
