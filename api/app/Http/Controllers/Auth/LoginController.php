<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required', 'email'
            ],
            'password' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request['email'])->first();

        if ($user) {

            if ($user->status == 0) {

                return response()->json(['success' => false, 'data' => 'User not active'], 200);
            }

            $login = $request->only('email', 'password');

            $remember = $request['remember_me'];

            if (Auth::attempt($login, $remember)) {
                
                $user = Auth::user();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->save();

                $access = [
                    'access_token' => $tokenResult->accessToken,
                    'token_type'   => 'Bearer',
                    'id' => Auth::user()->id,
                ];

                return response()->json(['success' => true, 'data' => $access], 200);
            } 

            return response()->json(['success' => false, 'data' => 'Correo o contraseña incorrectos'], 422);
        } else {

            return response()->json(['success' => false, 'data' => 'Usuario no existe'], 404);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['success' => true, 'data' => 'User logout'], 200);
    }

    public function reject()
    {
        return response()->json(['success' => false, 'errors' => 'Unauthorized'], 401);
    }
}
