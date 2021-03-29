<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStockMaps extends Model
{
    use HasFactory;

    protected $table = 'UserStockMaps';

    protected $fillable = ['user_id', 'stock_id', 'sort'];
}
