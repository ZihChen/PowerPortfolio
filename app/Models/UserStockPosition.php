<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStockPosition extends Model
{
    use HasFactory;

    protected $table = 'UserStockPositions';

    protected $fillable = ['user_id', 'stock_id', 'units', 'avg_open', 'target_position'];
}
