<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateUserValidator;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function loginPage()
    {
        return view('login_page');
    }

    public function signUp(CreateUserValidator $request)
    {
        $form = $request->all();

        $this->userService->createUser($form);

        return response('success', 200);
    }

    public function login(Request $request)
    {
        $validate_data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validate_data)) {
            return response(['msg' => 'Authorization failed'], 401);
        }

        $user = $request->user();

        $result = $user->createToken('Token');

        $result->token->save();

        return response(['access_token' => $result->accessToken], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response(['msg' => 'Logout success'], 200);
    }

    public function getUser(Request $request)
    {
        return response($request->user());
    }
}
