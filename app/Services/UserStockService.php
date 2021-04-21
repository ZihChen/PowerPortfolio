<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-19
 * Time: 23:32
 */

namespace App\Services;

use App\Models\UserStockMaps;

class UserStockService
{
    protected $userStockMapsModel;

    public function __construct(UserStockMaps $userStockMapsModel)
    {
        $this->userStockMapsModel = $userStockMapsModel;
    }

    public function attachStockByUser($user, $stock_id)
    {
        $stocks = $user->stocks;

        $maps = $this->userStockMapsModel->where('user_id', $user->id)->get();

        $sort = $stocks->isEmpty() ? 0 : $maps->max('sort') + 1;

        $this->userStockMapsModel->firstOrCreate([
            'user_id' => $user->id,
            'stock_id' => $stock_id,
        ], [
            'user_id' => $user->id,
            'stock_id' => $stock_id,
            'sort' => $sort,
        ]);
    }

    public function detachStockByUser($user, $stock_id)
    {
        $this->userStockMapsModel->where('user_id', $user->id)
            ->where('stock_id', $stock_id)->delete();
    }
}
