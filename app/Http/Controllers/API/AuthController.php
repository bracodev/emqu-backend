<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAPIController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('emqu')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Usuario no encontrado.', ['error' => 'Unauthorised']);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return  $this->sendResponse([], 'Successfully logged out');
    }
}
