<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class PasswordController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request['email'])->first();

        if ($user) {

            $request['token'] = uniqid(Str::random(20));

            $passwordReset = PasswordReset::updateOrCreate($request->all());

            $user->notify(new PasswordResetRequest($passwordReset->token));

            return response()->json(['success' => true, 'data' => 'Sent email reset password'], 200);
        }

        return response()->json(['success' => false, 'errors' => 'User not found'], 404);
    }

    public function rest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required'
            ],
            'password_confirmation' => [
                'required', 'same:password',
            ],
            'token' => [
                'required',
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $passwordReset = PasswordReset::where('token', $request['token'])->first();

        if ($passwordReset) {

            $user = User::where('email', $passwordReset->email)->first(); 
            
            $user->update([
                'password' => $request['password']
            ]);

            $passwordReset->delete();

            $user->notify(new PasswordResetSuccess());

            return response()->json(['success' => true, 'data' => 'Reset password success'], 200);
        }

        return response()->json(['success' => false, 'errors' => 'Token error'], 404);
    }
}
