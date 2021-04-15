<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'Stocks';

    protected $fillable = ['symbol', 'name', 'type', 'sector', 'industry', 'quote_latest_refresh', 'fiscal_latest_refresh'];

    public function daily_records()
    {
        return $this->hasMany(DailyStockRecord::class, 'stock_id', 'id');
    }

    public function latest_daily_record()
    {
        return $this->hasOne(DailyStockRecord::class, 'stock_id', 'id')->orderBy('date', 'desc')->latest();
    }

    public function fiscal_overviews()
    {
        return $this->hasMany(FiscalOverview::class, 'stock_id', 'id');
    }

    public function latest_fiscal_overview()
    {
        return $this->hasOne(FiscalOverview::class, 'stock_id', 'id')->orderBy('latest_refresh', 'desc')->latest();
    }
}
