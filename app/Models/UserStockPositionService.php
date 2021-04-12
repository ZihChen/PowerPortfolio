<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-04-11
 * Time: 23:55
 */

namespace App\Models;


class UserStockPositionService
{

    public function getStockPositionsByUser($user, $relation_fields = [])
    {
        return $user->stock_positions()->with($relation_fields)->get();
    }
}
