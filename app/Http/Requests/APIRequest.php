<?php
/**
 * Created by PhpStorm.
 * User: owlting
 * Date: 2021-03-28
 * Time: 14:48
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class APIRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response($validator->errors()));
    }
}
