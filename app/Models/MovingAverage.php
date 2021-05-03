<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovingAverage extends Model
{
    use HasFactory;

    protected $table = 'MovingAverage';

    protected $fillable = [
        'stock_id',
        'record_id',
        'date',
        'interval',
        'series_type',
        'time_period',
        'ma',
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
