<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{

    public function list(Request $request)
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

        $companies = User::all();
        return response()->json(['companies' => $companies], 200);
    }
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'contact' => 'required|unique:users|min:5|max:20',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'required',
            'business_name' => 'required',
            'business_type' => 'required',
            'number_of_products' => 'required|numeric',
            'password' => 'required|min:8',
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
            'business_name' => $request->input('business_name'),
            'business_type' => $request->input('business_type'),
            'number_of_products' => $request->input('number_of_products'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Use the accessToken method to retrieve the encrypted token
        // $token = $user->createToken('ReceiptManagement')->plainTextToken;

        // Directly include the token value in the response
        $response = [
            'user' => $user,
        ];

        // Return the response
        return response()->json($response, 200);
    }

    public function updateProfile(Request $request, $userId)
    {
        $user = User::find($userId);
        if (isset($user) == false) {
            return response()->json(['error' => 'user not found']);
        }


        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
            'contact' => 'required|min:5|max:20|unique:users,contact,' . $user->id,
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'required',
            'number_of_products' => 'required|numeric'
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

        // Update user profile
        $user = User::find($userId);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->contact = $request->input('contact');
        $user->address = $request->input('address');
        $user->number_of_products = $request->input('number_of_products');

        // Update profile image if provided
        if ($request->hasFile('logo')) {
            // Delete the old logo
            if ($user->logo) {
                Storage::delete('company/' . $user->logo);
            }

            // Store the new logo
            $logoPath = $request->file('logo')->store('company/logo');
            $logoPath = str_replace('company/', '', $logoPath);
            $user->logo = $logoPath;
        }

        $user->save();

        // Return updated user information
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }


    public function changePassword(Request $request, $userId)
    {
        $user = User::find($userId);

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
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

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }


    public function destroy(Request $request, $userId)
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
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json(['message' => 'Company deleted successfully'], 200);
    }


}
