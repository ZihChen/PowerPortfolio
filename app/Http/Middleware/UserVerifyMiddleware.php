<?php

namespace App\Http\Middleware;

use App\Traits\ErrorResponseCodeTrait;
use App\Traits\ErrorResponseMsgTrait;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserVerifyMiddleware
{
    use ErrorResponseCodeTrait, ErrorResponseMsgTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->is_verify == false) {
            throw new HttpResponseException(response()->json([
                'msg' => $this->VerifyFailedMsg,
                'error_code' => $this->userVerifyFailed
            ]));
        }

        return $next($request);
    }
}
