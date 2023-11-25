<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TwoFAController extends Controller
{
    public function store(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $find = UserCode::where('user_id', $user_id)
            ->where('code', $request->code)
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->first();

            if (!is_null($find)) {
                // Authentication successful, you can retrieve the user here
                $user = User::find($user_id);

                // Use plainTextToken for API authentication
                $token = $user->createToken('ReceiptManagement', ['*'])->plainTextToken;

                // Return the token and user information
                $response = [
                    'token' => $token,
                    'user' => $user,
                ];

                return response()->json($response, 200);
            } else {
                return response()->json(['error' => 'You entered the wrong code.'], 400);
            }
    }


    public function resend($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->generateCode($user_id);

        return response()->json(['message' => 'Code sent to your email.'], 200);
    }


    public function enableTwoFactorAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_two_fa' => 'required|boolean'
        ], [
            'is_two_fa.required' => 'The two-factor authentication field is required.',
            'is_two_fa.boolean' => 'The two-factor authentication field must be a boolean.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();
        $user->is_two_fa = true;
        $user->save();

        return response()->json(['message' => 'Two-factor authentication enabled'], 200);
    }

    public function disableTwoFactorAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_two_fa' => 'required|boolean'
        ], [
            'is_two_fa.required' => 'The two-factor authentication field is required.',
            'is_two_fa.boolean' => 'The two-factor authentication field must be a boolean.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();
        $user->is_two_fa = false;
        $user->save();

        return response()->json(['message' => 'Two-factor authentication disabled'], 200);
    }
}
