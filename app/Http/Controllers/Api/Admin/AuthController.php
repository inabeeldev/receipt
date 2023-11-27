<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // If the input is an email address
        $credentials = ['email' => $request->input('login'), 'password' => $request->input('password')];

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            $token = $user->createToken('ReceiptManagement', ['*'])->plainTextToken;

            $response = [
                'token' => $token,
                'user' => $user,
            ];

            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

}
