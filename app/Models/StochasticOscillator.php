<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StochasticOscillator extends Model
{
    use HasFactory;

    protected $table = 'StochasticOscillator';

    /**
     * string interval => default:daily
     * int fastk_period => default:9
     * int slowk_period => default:3
     * int slowd_period => default:3
     *
     * @var array
     */
    protected $fillable = [
        'stock_id',
        'record_id',
        'date',
        'interval',
        'fastk_period',
        'slowk_period',
        'slowd_period',
        'rsv',
        'stochastic_k',
        'stochastic_d',
    ];

    public function daily_record()
    {
        return $this->belongsTo(DailyStockRecord::class, 'record_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }
}
