<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // If the input is an email address
        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            $token = Str::random(120);
            Admin::where(['email' => $request->input('email')])->update([
                'auth_token' => $token
            ]);
            $response = [
                'token' => $token,
                'user' => $user,
            ];

            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function adminRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:admins',
            'contact' => 'required|unique:admins|min:5|max:20',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $imagePath = null;

        if (!empty($request->file('image'))) {
            $imagePath = $request->file('image')->store('admin/logo');
            $imagePath = str_replace('admin/', '', $imagePath);
        }
        $token = Str::random(120);
        $admin = Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'contact' => $request->input('contact'),
            'image' => $imagePath,
            'role' => $request->input('role'),
            'auth_token' => $token,
            'password' => Hash::make($request->input('password')),
        ]);

        // Use the accessToken method to retrieve the encrypted token


        // Directly include the token value in the response
        $response = [
            'token' => $token,
            'admin' => $admin,
        ];

        // Return the response
        return response()->json($response, 200);
    }


    public function editAdminProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'name' => 'required',
            'contact' => 'required|min:5|max:20',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required',
            'password' => 'sometimes|min:8', // Use 'sometimes' to make password optional
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        // Update profile information
        $admin->name = $request->input('name');
        $admin->contact = $request->input('contact');
        $admin->role = $request->input('role');

        // Handle image update if provided
        if (!empty($request->file('image'))) {
            $imagePath = $request->file('image')->store('admin/logo');
            $imagePath = str_replace('admin/', '', $imagePath);
            $admin->image = $imagePath;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }

        $admin->save();

        return response()->json(['message' => 'Profile updated successfully', 'admin' => $admin], 200);
    }
    public function deleteAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();
        if (isset($admin) == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        // Delete the admin record
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully'], 200);
    }

}
