<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ],[
            'username.required'=>'Username is required',
            'password.required'=>'Password is required',
        ]);

        $user=User::where('username', $request->username)->first();

        //check if user exist
        if(!$user) {
            return response()->json(['status'=>'error', 'message' => 'Username does not exist'], 404);
        }

        //check password
        if(!Hash::check($request->password, $user->password)) {
            return response()->json(['status'=>'error', 'message' => 'Incorrect password'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['status'=>'success', 'message'=>'Login Successful','token' => $token, 'user'=>$user, 'must_change_password'=>$user->must_change_password], 200);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    //Change Password
    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ],[
            'password.required'=>'New password is required',
            'password.min'=>'New password must be at least 6 characters',
            'password.confirmed'=>'New password and confirmation must be same',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'error','message'=>'Password Update Failed', 'errors'=>$validator->errors()], 422);
        }

        $user=$request->user();
        $user->update([
            'password'=>Hash::make($request->password),
            'must_change_password'=>false
        ]);
        return response()->json(['status'=>'success', 'message' => 'Password changed successfully'], 200);
    }
}
