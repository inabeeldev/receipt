<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'contact' => 'required|unique:users|min:5|max:20',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'required',
            'number_of_products' => 'required|numeric',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $logoPath = null;

        if (!empty($request->file('logo'))) {
            $logoPath = $request->file('logo')->store('company/logo');
            $logoPath = str_replace('company/', '', $logoPath);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'contact' => $request->input('contact'),
            'logo' => $logoPath,
            'address' => $request->input('address'),
            'number_of_products' => $request->input('number_of_products'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Use the accessToken method to retrieve the encrypted token
        $token = $user->createToken('ReceiptManagement')->plainTextToken;

        // Directly include the token value in the response
        $response = [
            'token' => $token,
            'user' => $user,
        ];

        // Return the response
        return response()->json($response, 200);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Attempt to authenticate the user with either username or email
        if (filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)) {
            // If the input is an email address
            $credentials = ['email' => $request->input('login'), 'password' => $request->input('password')];
        } else {
            // If the input is a username
            $credentials = ['username' => $request->input('login'), 'password' => $request->input('password')];
        }

        if (Auth::attempt($credentials)) {
            // Authentication successful, generate a new access token
            $user = Auth::user();
            $token = $user->createToken('ReceiptManagement')->plainTextToken;

            // Return the token and user information
            $response = [
                'token' => $token,
                'user' => $user,
            ];

            return response()->json($response, 200);
        } else {
            // Authentication failed
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }



}
