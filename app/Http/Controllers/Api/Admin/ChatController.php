<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\AdminChat;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function adminMessage(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'user_id' => 'required|exists:users,id',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();

        if (!$admin) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $chat = AdminMessage::create([
            'user_id' => $request->input('user_id'),
            'admin_id' => $admin->id,
            'sender_type' => 'admin',
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => $chat], 201);
    }

    public function userMessage(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $user = $request->user();

        $chat = AdminMessage::create([
            'user_id' => $user->id,
            'admin_id' => 1,
            'sender_type' => 'user',
            'message' => $request->input('message'),
        ]);

        return response()->json(['message' => $chat], 201);
    }

    public function getAdminMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();

        if (!$admin) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $messages = AdminMessage::where('admin_id', $admin->id)
        ->with('user')
        ->get();

        // Group messages by user_id
        $groupedMessages = $messages->groupBy('user_id');

        return response()->json(['messages' => $groupedMessages], 200);
    }

    public function getUserMessages(Request $request)
    {
        $user = $request->user();

        $messages = AdminMessage::where('user_id', $user->id)
            ->with('admin')
            ->get();

        return response()->json(['messages' => $messages], 200);
    }

    public function getUsersInAdminMessages(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();

        if (!$admin) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }
        // Assuming you have a model for the AdminMessage
        $users = AdminMessage::distinct('user_id')->pluck('user_id');

        // If you want to get user details, assuming you have a User model
        $userDetails = User::whereIn('id', $users)->get();

        return response()->json(['users' => $userDetails], 200);
    }

    public function adminUserMessages(Request $request, $userId)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $admin = Admin::where(['auth_token' => $request['token']])->first();

        if (!$admin) {
            return response()->json([
                'errors' => [
                    ['code' => 'admin', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        // Assuming you have a model for the AdminMessage
        $userMessages = AdminMessage::where('user_id', $userId)->get();

        return response()->json(['messages' => $userMessages], 200);
    }


    public function userAdminMessages(Request $request)
    {



        $user = $request->user();

        if (!$user) {
            return response()->json([
                'errors' => [
                    ['code' => 'user', 'message' => translate('Unauthenticated!')]
                ]
            ], 401);
        }

        // Assuming you have a model for the AdminMessage
        $userMessages = AdminMessage::where('user_id', $user->id)->get();

        return response()->json(['messages' => $userMessages], 200);
    }




}
