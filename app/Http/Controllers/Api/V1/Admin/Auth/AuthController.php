<?php


namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Incorrect login details'], 400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }


}
