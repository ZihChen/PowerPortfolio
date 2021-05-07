<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-05-07
 * Time: 00:47
 */

namespace App\Services;

use App\Models\UserStockPosition;

class UserPositionService
{
    protected $userPositionModel;

    public function __construct(UserStockPosition $userPositionModel)
    {
        $this->userPositionModel = $userPositionModel;
    }

    public function updateOrCreateUserPosition($user, $stock, $data = [])
    {
        return $this->userPositionModel->updateOrCreate([
            'user_id' => $user->id,
            'stock_id' => $stock->id,
        ], [
            'units' => isset($data['units']) ? $data['units'] : 0.0,
            'target_position' => isset($data['target_position']) ? $data['target_position'] : 0.0,
            'avg_open' => isset($data['avg_open']) ? $data['avg_open'] : 0.0,
        ]);
    }
}
