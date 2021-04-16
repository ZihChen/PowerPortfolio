<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelativeStrengthIndex extends Model
{
    use HasFactory;

    protected $table = 'RelativeStrengthIndex';

    /**
     * string $interval => default:daily
     * int $time_period => default:14
     * string $series_type => default:close
     *
     * @var array
     */
    protected $fillable = [
        'stock_id',
        'record_id',
        'date',
        'series_type',
        'interval',
        'time_period',
        'avg_gain',
        'avg_loss',
        'rsi',
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
