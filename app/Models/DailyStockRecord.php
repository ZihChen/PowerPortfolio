<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStockRecord extends Model
{
    use HasFactory;

    protected $table = 'DailyStockRecords';

    protected $fillable = [
        'date',
        'close_price',
        'high_price',
        'low_price',
        'change_percent',
        'rsv',
        'stochastic_k',
        'stochastic_d',
        'avg_gain',
        'avg_loss',
        'rsi',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function kd_indicators()
    {
        return $this->hasMany(StochasticOscillator::class, 'record_id', 'id');
    }

    public function rsi_indicator()
    {
        return $this->hasMany(RelativeStrengthIndex::class, 'record_id', 'id');
    }
}
