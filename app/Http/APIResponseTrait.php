<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-30
 * Time: 23:22
 */

namespace App\Http;


trait APIResponseTrait
{
    protected function response($data = [], $status = 0, $pagination = [], ...$extends)
    {
        $response['data'] = $data;
        $response['status'] = $status;

        if (!empty($pagination))
            $response['pagination'] = $pagination;

        return response()->json($response);
    }
}
