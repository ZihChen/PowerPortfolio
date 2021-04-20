<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-19
 * Time: 23:32
 */

namespace App\Services;


class UserStockService
{
    public function attachStockByUser($user, $stock_id)
    {
        $user->stocks()->attach($stock_id);
    }
}
