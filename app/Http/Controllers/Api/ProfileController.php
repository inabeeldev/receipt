<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'username' => 'required|unique:users,username,' . Auth::id(),
            'contact' => 'required|min:5|max:20|unique:users,contact,' . Auth::id(),
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'required',
            'number_of_products' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Update user profile
        $user = Auth::user();
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


    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }




}
