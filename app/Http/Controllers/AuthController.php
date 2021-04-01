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

    public function registerPage()
    {
        return view('register_page');
    }

    public function loginPage()
    {
        return view('login_page');
    }

    public function register(CreateUserValidator $request)
    {
        $form = $request->all();

        $this->userService->createUser($form);

        return redirect('login');
    }

    public function login(Request $request)
    {
        $validate_data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validate_data)) {

            return redirect()->back();
        }

        $user = $request->user();

        $result = $user->createToken('Token');

        $result->token->save();

        return redirect('dashboard');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('login');
    }

    public function getUser(Request $request)
    {
        return response($request->user());
    }
}
