<?php

namespace App\Http\Controllers;

use App\Services\AlphaAdvantageService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $alphaAdvantageService;

    public function __construct(AlphaAdvantageService $alphaAdvantageService)
    {
        $this->alphaAdvantageService = $alphaAdvantageService;
    }

    public function test()
    {

    }
}
