<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-31
 * Time: 12:24
 */

namespace App\Traits;


trait ErrorResponseCodeTrait
{
    public $success = 200;
    public $accepted = 202;
    public $badRequest = 400;
    public $notFound = 404;
}
