<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalOverview extends Model
{
    use HasFactory;

    protected $table = 'FiscalOverview';

    protected $fillable = ['eps', 'pe_ratio', 'roa_ttm', 'roe_ttm', 'profit_margin', 'operating_margin', 'ev_to_revenue', 'content', 'latest_refresh'];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
}
